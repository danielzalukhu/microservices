const { User } = require("../../../models");

module.exports = async(req, res) => {
    // get user id from URL parameter
    const userIds = req.query.user_ids || [];

    // create an options to show/return the attributes
    const sqlOptions = {
        attributes: ["id", "name", "email", "role", "profession", "avatar"],
    };

    // create logic if variable userIds is more than 1
    // or simply said that was an array of user ID from URL
    if (userIds.length) {
        sqlOptions.where = {
            id: userIds,
        };
    }

    // then find them
    // using sql injection like (SELECT * FROM users WHERE id IN [1,2]);
    const users = await User.findAll(sqlOptions);
    // and also return the response
    return res.json({
        status: "success",
        data: users,
    });
};