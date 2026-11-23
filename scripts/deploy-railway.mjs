import assert from 'node:assert/strict';

const { RAILWAY_TOKEN, RAILWAY_ENVIRONMENT_ID, IMAGE_REF } = process.env;
const serviceId = 'c4d557eb-636d-4c59-936b-2f7f0072b0e2';
assert(RAILWAY_TOKEN && RAILWAY_ENVIRONMENT_ID, 'Falta el acceso al ambiente');
assert(/^ghcr\.io\/patata-ni\/eje-del-golfo@sha256:[a-f0-9]{64}$/.test(IMAGE_REF ?? ''), 'Se requiere un digest validado');

async function railway(query, variables) {
  const response = await fetch('https://backboard.railway.com/graphql/v2', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Project-Access-Token': RAILWAY_TOKEN },
    body: JSON.stringify({ query, variables }),
  });
  assert(response.ok, `Railway HTTP ${response.status}`);
  const result = await response.json();
  assert(!result.errors?.length, 'Railway rechazó la operación de despliegue');
  return result.data;
}

const variables = { environmentId: RAILWAY_ENVIRONMENT_ID, serviceId };
const updated = await railway(`mutation($environmentId:String!, $serviceId:String!, $input:ServiceInstanceUpdateInput!) {
  serviceInstanceUpdate(environmentId:$environmentId, serviceId:$serviceId, input:$input)
}`, { ...variables, input: { source: { image: IMAGE_REF }, healthcheckPath: '/version', healthcheckTimeout: 300 } });
assert(updated.serviceInstanceUpdate, 'No se actualizó el artefacto');
const deployed = await railway(`mutation($environmentId:String!, $serviceId:String!) {
  serviceInstanceDeploy(environmentId:$environmentId, serviceId:$serviceId)
}`, variables);
assert(deployed.serviceInstanceDeploy, 'No se inició el despliegue');
console.log('Railway inició el despliegue del digest validado; se comprobará su versión y conexión MySQL.');
