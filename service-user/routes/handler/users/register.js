const { User } = require("../../../models");
const bcrypt = require("bcrypt");
const Validator = require("fastest-validator");
const v = new Validator();

module.exports = async(req, res) => {
    const schema = {
        name: "string|empty:false",
        email: "email|empty:false",
        password: "string|min:6",
        profession: "string|optional",
    };

    const validate = v.validate(req.body, schema);

    if (validate.length > 0) {
        return res.status(400).json({
            status: "error",
            message: validate,
        });
    }

    // Cari apakah email yang didaftarkan itu sudah ada di db apa belum
    const user = await User.findOne({
        where: { email: req.body.email },
    });

    if (user) {
        res.status(409).json({
            status: "error",
            message: "email already exist",
        });
    }

    const password = await bcrypt.hash(req.body.password, 10);

    const data = {
        password: password,
        name: req.body.name,
        email: req.body.email,
        profession: req.body.profession,
        role: "student",
    };

    const createUser = await User.create(data);

    return res.json({
        status: "success",
        data: {
            id: createUser.id,
            name: createUser.name,
            email: createUser.email,
            profession: createUser.profession,
            role: createUser.role,
        },
    });
};