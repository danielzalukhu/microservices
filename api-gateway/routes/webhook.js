const express = require("express");
const router = express.Router();
const webhookHandler = require("./handler/order-payment");

router.post("/", webhookHandler.webhook);

module.exports = router;