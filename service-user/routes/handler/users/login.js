const { User } = require("../../../models");
const bcrypt = require("bcrypt");
const Validator = require("fastest-validator");
const v = new Validator();

module.exports = async(req, res) => {
    const schema = {
        email: "string|empty:false",
        password: "string|min:6",
    };

    const validate = v.validate(req.body, schema);

    if (validate.length > 0) {
        return res.status(400).json({
            status: "error",
            message: validate,
        });
    }

    // Cari apakah email yang user input itu sudah ada atau sudah terdaftar di db apa belum
    const user = await User.findOne({
        where: { email: req.body.email },
    });

    if (!user) {
        return res.status(404).json({
            status: "error",
            message: "user not already register (not found)",
        });
    }

    // Validasi password dengan bcrypt method (compare)
    // Method ini ngebandingin 2 hal. Param 1 itu password yg belum di hash atau yg diinputin user
    // Param 2 itu password yang sudah di hash datang dari database

    const isValidPassword = await bcrypt.compare(req.body.password, user.password);

    if (!isValidPassword) {
        return res.status(404).json({
            status: "error",
            message: "invalid password",
        });
    }

    res.json({
        status: "success",
        data: {
            id: user.id,
            name: user.name,
            email: user.email,
            profession: user.profession,
            role: user.role,
            avatar: user.avatar,
        },
    });
};