const db = require('../config/db');

const EventModel = {
    getAll: (month, year, callback) => {
        db.query('SELECT * FROM events WHERE month = ? AND year = ?', [month, year], callback);
    },
    create: (data, callback) => {
        db.query('INSERT INTO events SET ?', data, callback);
    },
    update: (id, detail, callback) => {
        db.query('UPDATE events SET detail = ? WHERE id = ?', [detail, id], callback);
    },
    delete: (id, callback) => {
        db.query('DELETE FROM events WHERE id = ?', [id], callback);
    }
};

module.exports = EventModel;