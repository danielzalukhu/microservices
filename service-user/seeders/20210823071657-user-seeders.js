"use strict";
const bcrypt = require("bcrypt");

module.exports = {
    up: async(queryInterface, Sequelize) => {
        await queryInterface.bulkInsert("users", [{
                name: "Daniel",
                profession: "Full-Stack Developer",
                role: "student",
                email: "keperluansamping@gmail.com",
                password: await bcrypt.hash("untukapa123", 10),
                created_at: new Date(),
                updated_at: new Date(),
            },
            {
                name: "Natalia",
                profession: "Administrator",
                role: "admin",
                email: "natalia@gmail.com",
                password: await bcrypt.hash("natalia123", 10),
                created_at: new Date(),
                updated_at: new Date(),
            },
        ]);
    },

    down: async(queryInterface, Sequelize) => {
        await queryInterface.bulkDelete("users", null, {});
    },
};