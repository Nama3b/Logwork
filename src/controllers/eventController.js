const EventModel = require('../models/eventModel');

exports.getEvents = (req, res) => {
    const { month, year } = req.query;
    EventModel.getAll(month, year, (err, results) => {
        if (err) return res.status(500).send(err);
        res.json(results);
    });
};

exports.createEvent = (req, res) => {
    const { day, month, year, time_slot, detail } = req.body;
    EventModel.create({ day, month, year, time_slot, detail }, (err) => {
        if (err) return res.status(500).send(err);
        res.send('Sự kiện đã được lưu!');
    });
};

exports.updateEvent = (req, res) => {
    const { id } = req.params;
    const { detail } = req.body;
    EventModel.update(id, detail, (err) => {
        if (err) return res.status(500).send(err);
        res.send('Sự kiện đã được cập nhật!');
    });
};

exports.deleteEvent = (req, res) => {
    const { id } = req.params;
    EventModel.delete(id, (err) => {
        if (err) return res.status(500).send(err);
        res.send('Sự kiện đã được xóa!');
    });
};