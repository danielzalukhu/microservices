const bcrypt = require('bcrypt');
const { User } = require('../../../models');
const Validator = require('fastest-validator');
const v = new Validator();

module.exports = async (req, res) => {
    const schema = {
        name: 'string|empty:false',
        email: 'email|empty:false',
        password: 'string|min:6',
        profession: 'string|optional'
    }

    // req.body artinya mengambil semua field yang dikirim dari front-end
    // validate ini mengembalikannya bentuk array
    const validate = v.validate(req.body, schema);

    if (validate.length) {
        return res.status(400).json({
            status: 'error',
            message: validate
        });
    }

    const user = await User.findOne({
        where: { email: req.body.email }
    });

    // 409 itu return konflik kalau data udh ada 
    if (user) {
        return res.status(409).json({
            status: 'error',
            message: 'email already exists'
        })
    }
    // Jika emailnya itu belum terdaftar maka kita bcrypt dulu user passwordnya
    const password = await bcrypt.hash(req.body.password, 10);
    // Habis itu buat handler untuk nampung semua data yg di input dalam bentuk object dengan suatu
    // variable yaitu data
    const data = {
        password,
        name: req.body.name,
        email: req.body.email,
        profession: req.body.profession,
        role: 'student'
    }

    const createdUser = await User.create(data);

    return res.json({
        status: 'success',
        data: {
            id: createdUser.id,
            email: createdUser.email,
            name: createdUser.name,
            profession: createdUser.profession,
            role: createdUser.role
        }
    })
}