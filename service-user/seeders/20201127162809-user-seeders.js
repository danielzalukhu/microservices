'use strict';
const bcrypt = require('bcrypt');

module.exports = {
  up: async (queryInterface, Sequelize) => {
    await queryInterface.bulkInsert('users', [
      {
        name: "Natalia Fernanda",
        profession: "Admin Micro",
        role: "admin",
        email: "nataliafrnda@gmail.com",
        password: await bcrypt.hash('natalia', 10),
        created_at: new Date(),
        updated_at: new Date()
      },
      {
        name: "Daniel Zalukhu",
        profession: "Web Developer",
        role: "student",
        email: "danielzalukhu@gmail.com",
        password: await bcrypt.hash('kristo', 10),
        created_at: new Date(),
        updated_at: new Date()
      }      
    ], {});
  },

  down: async (queryInterface, Sequelize) => {
    await queryInterface.bulkDelete('users', null, {});    
  }
};
