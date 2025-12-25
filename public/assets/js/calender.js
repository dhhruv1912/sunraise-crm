document.addEventListener('DOMContentLoaded', () => {
    let currentDate = new Date();

    const weeksEl = document.getElementById('calendarWeeks');
    const monthTitle = document.getElementById('monthTitle');

    const monthNames = [
        'January','February','March','April','May','June',
        'July','August','September','October','November','December'
    ];
    function markEmiDates() {
    const today = new Date().toISOString().split('T')[0];

    EMI_DATES.forEach(emidate => {
        const cell = document.querySelector(`[data-date="${emidate}"]`);
        if (!cell) return;

        // reset
        cell.classList.remove(
            'bg-label-success',
            'bg-label-warning',
            'bg-label-danger',
            'bg-label-info',
            'bg-label-primary',
            'bg-success',
            'bg-warning',
            'bg-primary',
            'bg-gradient',
            'text-white'
        );
        cell.classList.add(
            "d-flex",
            "flex-column",
            "align-items-baseline",
            "justify-content-between",
        )

        const paidAt = PAID_EMI_DATES[emidate]
            ? PAID_EMI_DATES[emidate].split('T')[0]
            : null;
        const pill = document.createElement('span')
        pill.classList = "badge rounded-pill"
        pill.textContent = EMIS[emidate]
        if (paidAt) {
            if (paidAt < emidate) {
                // 🔵 Advance paid
                cell.classList.add('bg-label-primary', 'bg-gradient');
                pill.classList.add('bg-primary')
                cell.title = `EMI paid in advance on ${paidAt}`;
            }
            else if (emidate === today) {
                // 🟢 Paid today
                cell.classList.add('bg-success', 'bg-gradient', 'text-white');
                pill.classList.add('bg-label-success')
                cell.title = `EMI paid today`;
            }
            else {
                // ✅ Paid (on / after due date)
                cell.classList.add('bg-label-success');
                pill.classList.add('bg-success')
                cell.title = `EMI paid on ${paidAt}`;
            }
        }
        else if (emidate === today) {
            // 🟡 Due today
            cell.classList.add('bg-warning', 'bg-gradient');
            pill.classList.add('bg-label-warning')
            cell.title = 'EMI due today';
        }
        else if (emidate < today) {
            // 🔴 Overdue
            cell.classList.add('bg-label-danger');
            pill.classList.add('bg-danger')
            cell.title = 'EMI overdue';
        }
        else {
            // 🔵 Upcoming
            cell.classList.add('bg-label-info');
            pill.classList.add('bg-info')
            cell.title = 'Upcoming EMI';
        }

        cell.classList.add('hasInfo');
        pill.classList.add('fs-tiny','py-1');
        cell.appendChild(pill)
    });
}




    function renderCalendar(dateObj) {
        const today = new Date();
        const year = dateObj.getFullYear();
        const month = dateObj.getMonth();

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const prevMonthLastDay = new Date(year, month, 0);

        let startDay = firstDay.getDay() || 7;
        let date = 1;
        let nextMonthDate = 1;

        monthTitle.textContent = `${monthNames[month]} ${year}`;
        weeksEl.innerHTML = '';

        for (let w = 0; w < 6; w++) {
            const week = document.createElement('div');
            week.className = 'd-flex calendar-cols';

            for (let d = 1; d <= 7; d++) {
                const span = document.createElement('span');
                span.classList.add('cell', 'squere', 'border');

                if (w === 0 && d < startDay) {
                    span.textContent = prevMonthLastDay.getDate() - (startDay - d - 1);
                    span.classList.add('last-month');
                }
                else if (date > lastDay.getDate()) {
                    span.textContent = nextMonthDate++;
                    span.classList.add('next-month');
                }
                else {
                    const mm = String(month + 1).padStart(2, '0');
                    const dd = String(date).padStart(2, '0');
                    
                    span.textContent = String(date).padStart(2, '0');
                    span.dataset.date = `${year}-${mm}-${dd}`;

                    if (
                        date === today.getDate() &&
                        month === today.getMonth() &&
                        year === today.getFullYear()
                    ) {
                        span.classList.add('active', 'bg-label-secondary','border-secondary');
                    }

                    date++;
                }

                week.appendChild(span);
            }

            weeksEl.appendChild(week);
            if (date > lastDay.getDate()) break;
        }
    }

    // Initial render
    renderCalendar(currentDate);
    markEmiDates();

    // ⬅ Prev month
    document.getElementById('prevMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
        markEmiDates();
    });

    // ➡ Next month
    document.getElementById('nextMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
        markEmiDates();
    });

    // EMI_DATES.forEach(emidate => {
    //     console.log(document.getElementsByClassName(emidate))
    //     document.getElementsByClassName(emidate) ? document.getElementsByClassName(emidate)[0].classList.add('bg-label-warning') : null
    // });

    var app = {
        settings: {
            container: $('.calendar'),
            calendar: $('.front'),
            days: $('.weeks span.hasInfo'),
            form: $('.back'),
            input: $('.back input'),
            buttons: $('.back button')
        },

        init: function() {
            instance = this;
            settings = this.settings;
            this.bindUIActions();
        },

        swap: function(currentSide, desiredSide) {
            settings.container.toggleClass('flip');

        currentSide.fadeOut(900);
        currentSide.hide();
        desiredSide.show();

        },

        bindUIActions: function() {
            // settings.days.on('click', function(e){
            //     console.log('days',e);
                
            //     instance.swap(settings.calendar, settings.form);
            //     settings.input.focus();
            // });

            // settings.buttons.on('click', function(){
            //     console.log('ss');
                
            //     instance.swap(settings.form, settings.calendar);
            // });
        }
    }
    app.init();
});

function getSuffix(d) {
    if (d > 3 && d < 21) return 'th';
    switch (d % 10) {
        case 1: return 'st';
        case 2: return 'nd';
        case 3: return 'rd';
        default: return 'th';
    }
}



