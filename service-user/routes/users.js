const express = require("express");
const router = express.Router();
const usersHandler = require("./handler/users");

router.post("/register", usersHandler.register);
router.post("/login", usersHandler.login);
router.post("/logout", usersHandler.logout);
router.put("/:id", usersHandler.update);
router.get("/:id", usersHandler.getuser);
router.get("/", usersHandler.listuser);

module.exports = router;