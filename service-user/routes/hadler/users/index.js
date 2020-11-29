const register = require('./register');
const login = require('./login');
const updateUser = require('./updateUser');
const getUser = require('./getUser');
const getListUser = require('./getListUser');
const logout = require('./logout');

module.exports = {
    register,
    login, 
    updateUser,
    getUser,
    getListUser,
    logout
}