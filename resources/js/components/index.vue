<style>
.body {
    font-family: Arial, sans-serif;
    text-align: center;
    margin: 0;
    padding: 0;
}

h1 {
    text-transform: uppercase;
}

.calendar {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
    gap: 5px;
    max-width: 100%;
    margin: 20px auto;
    padding: 10px;
}

.day {
    border: 1px solid #ccc;
    padding: 10px 5px;
    min-height: 60px;
    cursor: pointer;
    position: relative;
    background: #f9f9f9;
    border-radius: 5px;
    font-size: 14px;
}

.event {
    color: white;
    padding: 3px;
    margin-top: 3px;
    border-radius: 3px;
    font-size: 12px;
}

.popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #c4c4c4;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
    width: 90%;
    max-width: 400px;
}

.popup button {
    width: 100%;
    padding: 10px;
    margin: 5px 0;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
}

.popup input {
    width: calc(100% - 17px);
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
    font-size: 10px;
    padding: 1px 3px;
    right: -7px;
    top: -3px;
    border-radius: 50%;
    color: white;
    cursor: pointer;
}

.btn-action {
    display: flex;
}

.btn-action button {
    margin: 2px;
}

.save-btn {
    border: none;
}

.cancel-btn {
    border: 1px solid white;
    background: #c4c4c4;
}

</style>

<template>
    <div class="body">
        <h1>{{ new Date().toLocaleString('vi-VN', { day: 'numeric', month: 'long'}) }}</h1>
        <div class="calendar">
            <div v-for="day in daysInMonth" :key="day" class="day" @click="openPopup(day)">
                {{ day }}
                <div v-for="(event, time) in events[day]" :key="time" :class="['event', time.toLowerCase()]">
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
        const timeOrder = { "Sáng": 1, "Chiều": 2, "Tối": 3 };

        const getDaysInMonth = () => {
            const date = new Date();
            const month = date.getMonth();
            const year = date.getFullYear();
            return new Array(new Date(year, month + 1, 0).getDate()).fill().map((_, i) => i + 1);
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

        const openPopup = (day, time = null) => {
            const today = new Date().getDate();
            if (day < today) {
                alert("Bạn không thể thêm sự kiện cho ngày đã qua.");
                return;
            }
            selectedDay.value = day;
            selectedTimeSlot.value = time;
            eventDetail.value = time && events.value[day]?.[time]?.detail || '';
            selectedEventId.value = time && events.value[day]?.[time]?.id || null;
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

        const deleteEvent = async () => {
            const confirmDelete = window.confirm("Bạn có chắc chắn muốn xóa sự kiện này?");
            if (!confirmDelete) return;

            const eventId = events.value;
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
            deleteEvent,
            timeOrder
        };
    }
};
</script>
