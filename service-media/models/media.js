module.exports = ( sequelize, DataTypes ) => {
    const Media = sequelize.define('Media', {
        id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true,
            allowNull: false,
        },
        image: {
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
        tableName: 'media'
    });

    return Media;
}