require('dotenv').config();

// Node/Express
const express = require('express');
const http = require('http');
const path = require('path');
const bodyParser = require('body-parser');

const router = require('./src/router');
const syncServiceDetails = require('./src/sync_service_details');

// Create Express webapp
const app = express();
app.use(express.static(path.join(__dirname, 'public')));

// Add body parser for Notify device registration
app.use(bodyParser.urlencoded({extended: true}));
app.use(bodyParser.json());

app.use(router);

// Get Sync Service Details for lazy creation of default service if needed
syncServiceDetails();

// Create http server and run it
//const server = http.createServer(app);
const port = process.env.PORT || 3200;
var fs = require('fs');
var https = require('https');
const options = {
  key: fs.readFileSync('../../../../etc/letsencrypt/archive/tsicmentorapp.org/privkey1.pem'),
  cert: fs.readFileSync('../../../../etc/letsencrypt/archive/tsicmentorapp.org/fullchain1.pem')
};
var server = https.createServer(options, app).listen(3200, function (req, res) {
  console.log(`Server now listening 3200 https.`);
});

module.exports = app;
