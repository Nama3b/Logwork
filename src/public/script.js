const apiBaseUrl = "http://localhost:3000/api/events";

// Khởi tạo danh sách tháng & năm
const monthSelect = document.getElementById("month");
const yearSelect = document.getElementById("year");
const today = new Date();
const currentYear = today.getFullYear();
const currentMonth = today.getMonth() + 1;

// Thêm danh sách tháng
for (let i = 1; i <= 12; i++) {
    let option = document.createElement("option");
    option.value = i;
    option.textContent = `Tháng ${i}`;
    if (i === currentMonth) option.selected = true;
    monthSelect.appendChild(option);
}

// Thêm danh sách năm (5 năm trước & 5 năm sau)
for (let i = currentYear - 5; i <= currentYear + 5; i++) {
    let option = document.createElement("option");
    option.value = i;
    option.textContent = i;
    if (i === currentYear) option.selected = true;
    yearSelect.appendChild(option);
}

// Hàm tải danh sách sự kiện từ Backend
async function loadEvents() {
    const month = monthSelect.value;
    const year = yearSelect.value;

    try {
        const response = await fetch(`${apiBaseUrl}/${month}/${year}`);
        const events = await response.json();

        const tableBody = document.querySelector("#calendar tbody");
        tableBody.innerHTML = "";

        events.forEach(event => {
            const row = document.createElement("tr");

            row.innerHTML = `
                <td>${event.day}</td>
                <td>${event.time_slot}</td>
                <td>${event.detail}</td>
                <td><button onclick="deleteEvent(${event.id})">Xóa</button></td>
            `;

            tableBody.appendChild(row);
        });
    } catch (error) {
        console.error("Lỗi khi tải sự kiện:", error);
    }
}

// Hàm thêm sự kiện mới
async function addEvent() {
    const day = document.getElementById("day").value;
    const month = monthSelect.value;
    const year = yearSelect.value;
    const timeSlot = document.getElementById("timeSlot").value;
    const detail = document.getElementById("detail").value;

    // Không cho phép thêm sự kiện vào ngày quá khứ
    const todayDate = new Date();
    const selectedDate = new Date(year, month - 1, day);
    if (selectedDate < todayDate) {
        alert("Không thể thêm sự kiện vào ngày đã qua!");
        return;
    }

    try {
        const response = await fetch(`${apiBaseUrl}/add`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ day, month, year, time_slot: timeSlot, detail }),
        });

        if (response.ok) {
            alert("Thêm sự kiện thành công!");
            loadEvents();
        } else {
            alert("Lỗi khi thêm sự kiện!");
        }
    } catch (error) {
        console.error("Lỗi khi gửi yêu cầu:", error);
    }
}

// Hàm xóa sự kiện
async function deleteEvent(eventId) {
    try {
        const response = await fetch(`${apiBaseUrl}/delete/${eventId}`, {
            method: "DELETE",
        });

        if (response.ok) {
            alert("Xóa sự kiện thành công!");
            loadEvents();
        } else {
            alert("Lỗi khi xóa sự kiện!");
        }
    } catch (error) {
        console.error("Lỗi khi xóa sự kiện:", error);
    }
}

// Load sự kiện khi trang được mở
loadEvents();
