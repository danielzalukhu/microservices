const express = require('express');
const router = express.Router();
const userHandler = require('./hadler/users');

router.post('/register', userHandler.register);
router.post('/login', userHandler.login);
router.put('/update/:id', userHandler.updateUser);
router.get('/:id', userHandler.getUser);
router.get('/', userHandler.getListUser);
router.post('/logout', userHandler.logout);

module.exports = router;
