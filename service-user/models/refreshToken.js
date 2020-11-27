module.exports = ( sequelize, DataTypes ) => {
    const RefreshToken = sequelize.define('RefreshToken', {
        id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true,
            allowNull: false
        },
        token: {
            type: DataTypes.TEXT,
            allowNull: false
        },
        user_id: {
            type: DataTypes.INTEGER,
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
        tableName: 'refresh_tokens',
        timestamps: true
    });

    return RefreshToken;
}