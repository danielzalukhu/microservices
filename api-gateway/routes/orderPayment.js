const express = require("express");
const router = express.Router();
const orderPaymentHandler = require("./handler/order-payment");
const verifyToken = require("../middleware/verifyToken");
const permission = require("../middleware/permission");

router.get("/", verifyToken, permission("admin", "student"), orderPaymentHandler.getOrder);

module.exports = router;