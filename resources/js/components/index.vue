<style>
.body {
    font-family: Arial, sans-serif;
    text-align: center;
    margin: 0;
    padding: 0;
    background-color: #f4f4f9;
}

h1 {
    text-transform: uppercase;
    font-size: 24px;
    margin-top: 20px;
    font-weight: bold;
    color: #333;
}

.calendar {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 10px;
    max-width: 90%;
    margin: 20px auto;
    padding: 10px;
}

.day {
    border: 1px solid #ddd;
    padding: 15px 6px;
    min-height: 70px;
    cursor: pointer;
    position: relative;
    background: white;
    border-radius: 8px;
    font-size: 12px;
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.day:hover {
    transform: translateY(-5px);
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15);
}

.day i {
    font-size: 10px;
}

.event {
    color: white;
    padding: 4px 0;
    margin-top: 5px;
    border-radius: 3px;
    font-size: 12px;
    transition: opacity 0.3s ease-in-out;
}

.popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #ffffff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    width: 90%;
    max-width: 400px;
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translate(-50%, -55%);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%);
    }
}

.popup button {
    width: 100%;
    padding: 10px;
    margin: 5px 0;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.2s;
}

.popup button:hover {
    background: #ddd;
}

.popup input {
    width: calc(100% - 20px);
    padding: 8px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.time-btn {
    background-color: #f1f1f1;
    border: 1px solid #ccc;
    padding: 10px;
    margin: 5px;
    cursor: pointer;
    transition: background 0.2s;
}

.time-btn:hover {
    background: #ddd;
}

.selected {
    color: white;
    background-color: #505151;
}

#btnSáng.selected, .sáng {
    background-color: #24abab;
}

#btnChiều.selected, .chiều {
    background-color: #ff9800;
}

#btnTối.selected, .tối {
    background-color: #2a3a93;
}

.event {
    position: relative;
}

button.delete-btn {
    position: absolute;
    background: #7a7a7a;
    border: none;
    font-size: 12px;
    padding: 2px 5px;
    right: -7px;
    top: -3px;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    transition: background 0.2s;
}

button.delete-btn:hover {
    background: #555;
}

.btn-action {
    display: flex;
    justify-content: space-between;
}

.btn-action button {
    margin: 2px;
}

.save-btn {
    border: none;
    background-color: #4CAF50;
    color: white;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.2s;
}

.save-btn:hover {
    background-color: #45a049;
}

.cancel-btn {
    border: 1px solid white;
    background: #c4c4c4;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.2s;
}

.cancel-btn:hover {
    background: #bbb;
}
</style>

<template>
    <div class="body">
        <h1>{{ new Date().toLocaleString('vi-VN', { day: 'numeric', month: 'long'}) }}</h1>
        <div class="calendar">
            <div v-for="day in daysInMonth" :key="day.date" class="day" @click="openPopup(day.date)">
                {{ day.date }} <i>({{ day.weekday }})</i>
                <div v-for="(event, time) in events[day.date]" :key="time" :class="['event', time.toLowerCase()]" @click.stop="openPopup(day.date, event.id)">
                    {{ time }}: {{ event.detail }}
                    <button class="delete-btn" @click.stop="deleteEvent(event.id)">&times;</button>
                </div>
            </div>
        </div>

        <div v-if="showPopup" class="popup">
            <h3>Chọn thời gian</h3>
            <button v-for="time in timeSlots" :key="time" @click="selectTime(time)"
                    :class="['time-btn', { selected: selectedTimeSlot === time }]">
                {{ time }}
            </button>
            <input type="text" v-model="eventDetail" placeholder="Nhập nội dung"/>
            <div class="btn-action">
                <button class="save-btn" @click="saveEvent">Lưu</button>
                <button class="cancel-btn" @click="showPopup = false">Hủy</button>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
    setup() {
        const daysInMonth = ref([]);
        const selectedDay = ref(null);
        const selectedTimeSlot = ref(null);
        const eventDetail = ref('');
        const selectedEventId = ref(null);
        const events = ref({});
        const showPopup = ref(false);

        const timeSlots = ["Sáng", "Chiều", "Tối"];
        const weekdays = ["Chủ nhật", "Thứ hai", "Thứ ba", "Thứ tư", "Thứ năm", "Thứ sáu", "Thứ bảy"];

        const getDaysInMonth = () => {
            const date = new Date();
            const month = date.getMonth();
            const year = date.getFullYear();
            const days = [];
            for (let i = 1; i <= new Date(year, month + 1, 0).getDate(); i++) {
                const dayDate = new Date(year, month, i);
                days.push({
                    date: i,
                    weekday: weekdays[dayDate.getDay()]
                });
            }
            return days;
        };

        const fetchEvents = async () => {
            try {
                const response = await axios.get('/api/list-event');
                const formattedEvents = {};
                response.data.data.forEach(event => {
                    const { id, day, time_slot, detail } = event;
                    if (!formattedEvents[day]) {
                        formattedEvents[day] = {};
                    }
                    formattedEvents[day][time_slot] = { id, detail };
                });
                events.value = formattedEvents;
            } catch (error) {
                console.error('Error fetching events:', error);
            }
        };

        const openPopup = (day, id) => {
            console.log("xxxxxx",id);
            const today = new Date().getDate();
            if (day < today) {
                alert("Bạn không thể thêm sự kiện cho ngày đã qua.");
                return;
            }
            selectedEventId.value = id;
            selectedDay.value = day;
            selectedTimeSlot.value = null;
            eventDetail.value = '';
            showPopup.value = true;
        };

        const selectTime = (time) => {
            selectedTimeSlot.value = time;
        };

        const saveEvent = async () => {
            if (!selectedTimeSlot.value || !eventDetail.value.trim()) {
                alert("Vui lòng chọn thời gian và nhập nội dung.");
                return;
            }

            try {
                await axios.post('/api/save-event', {
                    id: selectedEventId.value,
                    day: selectedDay.value,
                    time_slot: selectedTimeSlot.value,
                    detail: eventDetail.value.trim()
                });
                await fetchEvents();
                showPopup.value = false;
            } catch (error) {
                console.error('Error saving event:', error);
            }
        };

        const deleteEvent = async (eventId) => {
            const confirmDelete = window.confirm("Bạn có chắc chắn muốn xóa sự kiện này?");
            if (!confirmDelete) return;

            try {
                await axios.delete('/api/delete-event', { data: { id: eventId } });
                await fetchEvents();
            } catch (error) {
                console.error('Error deleting event:', error);
            }
        };

        onMounted(() => {
            daysInMonth.value = getDaysInMonth();
            fetchEvents();
        });

        return {
            daysInMonth,
            selectedDay,
            selectedTimeSlot,
            eventDetail,
            selectedEventId,
            events,
            showPopup,
            timeSlots,
            openPopup,
            selectTime,
            saveEvent,
            deleteEvent
        };
    }
};
</script>
