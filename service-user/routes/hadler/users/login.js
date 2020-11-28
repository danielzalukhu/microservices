const bcrypt = require('bcrypt');
const { User } = require('../../../models');
const Validator = require('fastest-validator');
const v = new Validator();

module.exports = async (req, res) => {
    // Validasi
    const schema = {
        email: 'email|empty:false',
        password: 'string|min:6',
    }
    // Check validasi berdasarkan inputan user 
    const validate = v.validate(req.body, schema);
    // Jika validasinya ada / validadate ngereturn array > 1 (ada isi)
    if (validate.length) {
        return res.status(400).json({
            status: 'error',
            message: validate
        });
    }
    // End of validasi

    // Jika validasi berhasil maka
    // 1. Cek dulu di database email yg diinput user itu ada atau nggak di database
    const user = await User.findOne({
        where: { email: req.body.email }
    });
    // 1. Jika emailnya yg diinputin user gk ada di database
    // return error
    if (!user) {
        return res.status(404).json({
            status: 'error',
            message: 'user not found'
        });
    }
    // 2. Jika user yang diinputin ada
    // Kemudian, passwordnya kita validasi dulu dengan bcrypt
    // di compare param1 itu berdasarkan input user makanya req.body.passsword
    // param2 itu password yg di database 
    const isValidPassword = await bcrypt.compare(req.body.password, user.password);
    // 3. Jika passwordnya gk valid maka return respon error
    if (!isValidPassword) {
        return res.status(404).json({
            status: 'error',
            message: 'user not found'
        });
    }
    // 4. Namun jika berhasil maka return respon success dengan data2 user yg login
    res.json({
        status: 'success',
        data: {
            id: user.id,
            email: user.email,
            name: user.name,
            role: user.role,
            avatar: user.avatar,
            profession: user.profession
        }
    })
    // End
}