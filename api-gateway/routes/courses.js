const express = require("express");
const router = express.Router();
const mentorHandler = require("./handler/courses/mentor");

/* Mentor route listing. */
router.get("/", mentorHandler.read);
router.get("/:id", mentorHandler.show);
router.post("/", mentorHandler.create);

module.exports = router;
