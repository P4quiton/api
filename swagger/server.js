const express = require('express');
const swaggerUi = require('swagger-ui-express');
const YAML = require('yamljs');
const path = require('path');

const app = express();
const PORT = 3000;

const swaggerDocument = YAML.load(
    path.join(__dirname, '../openapi/v3/openapi.yaml')
);

app.use('/api-docs', swaggerUi.serve, swaggerUi.setup(swaggerDocument));

app.get('/', (req, res) => {
    res.send('Servidor Swagger funcionando');
});

app.listen(PORT, () => {
    console.log(`Swagger UI disponible en http://localhost:${PORT}/api-docs`);
});