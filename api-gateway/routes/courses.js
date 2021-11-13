const express = require("express");
const router = express.Router();
const mentorHandler = require("./handler/courses/mentor");
const courseHandler = require("./handler/courses/course");

/* Mentor route listing. */
router.get("/mentor/", mentorHandler.read);
router.post("/mentor/", mentorHandler.create);
router.get("/mentor/:id", mentorHandler.show);
router.put("/mentor/:id", mentorHandler.update);
router.delete("/mentor/:id", mentorHandler.destroy);

/* Course route listing. */
router.get("/catalog", courseHandler.get);
router.post("/catalog", courseHandler.create);
router.get("/catalog/:id", courseHandler.show);

module.exports = router;