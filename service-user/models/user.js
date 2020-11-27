module.exports = ( sequelize, DataTypes ) => {
    const User = sequelize.define('User', {
        id: {
            type: DataTypes.INTEGER,
            autoIncrement: true,
            primaryKey: true,
            allowNull: false
        },
        name: {
            type: DataTypes.STRING,
            allowNull: false
        },
        profession: {
            type: DataTypes.STRING,
            allowNull: true
        },
        avatar: {
            type: DataTypes.STRING,
            allowNull: true
        },
        role: {
            type: DataTypes.ENUM,
            values: ['admin', 'student'],
            allowNull: false,
            defaultValue: 'student'
        },
        email: {
            type: DataTypes.STRING,
            allowNull: false,
            unique: true,
        },
        password: {
            type: DataTypes.STRING,
            allowNull: false
        },
        createdAt: {
            // Field digunakan untuk menyinkronkan dengan nama column yg dimigrate.
            // createdAt ini default dari sequelize-nya
            field: 'created_at',
            type: DataTypes.DATE,
            allowNull: false
        },
        updatedAt: {
            // Field digunakan untuk menyinkronkan dengan nama column yg dimigrate.
            // updatedAt ini default dari sequelize-nya
            field: 'updated_at',
            type: DataTypes.DATE,
            allowNull: false
        }
    }, {
        tableName: 'users',
        timestamps: true
    });

    return User;
}