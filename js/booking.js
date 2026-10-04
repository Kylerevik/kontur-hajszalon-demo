(() => {
    const form = document.getElementById('booking-form');
    if (!form) {
        return;
    }

    const dateInput = form.querySelector('#booking-date');
    const slotList = form.querySelector('#slot-list');
    const slotMessage = form.querySelector('#slot-message');
    const defaultMessage = slotMessage.textContent;
    let pendingRequest = null;

    const selectedService = () => form.querySelector('input[name="service_id"]:checked');
    const selectedTime = () => form.querySelector('input[name="start_time"]:checked');

    function createSlot(time, preselected) {
        const wrapper = document.createElement('div');
        wrapper.className = 'slot';

        const input = document.createElement('input');
        input.className = 'slot__input';
        input.type = 'radio';
        input.name = 'start_time';
        input.id = `slot-${time.replace(':', '')}`;
        input.value = time;
        input.required = true;
        input.checked = time === preselected;

        const label = document.createElement('label');
        label.className = 'slot__label';
        label.htmlFor = input.id;
        label.textContent = time;

        wrapper.append(input, label);
        return wrapper;
    }

    async function loadSlots() {
        if (pendingRequest) {
            pendingRequest.abort();
        }
        slotList.replaceChildren();

        const service = selectedService();
        if (!service || !dateInput.value) {
            slotMessage.textContent = defaultMessage;
            return;
        }

        pendingRequest = new AbortController();
        slotMessage.textContent = 'Szabad időpontok keresése…';

        try {
            const query = new URLSearchParams({ service: service.value, date: dateInput.value });
            const response = await fetch(`${form.dataset.slotsUrl}?${query}`, {
                signal: pendingRequest.signal,
                headers: { Accept: 'application/json' },
            });
            if (!response.ok) {
                throw new Error('A lekérdezés nem sikerült.');
            }

            const data = await response.json();
            slotList.replaceChildren(...data.slots.map((time) => createSlot(time, slotList.dataset.selected)));
            slotMessage.textContent = data.message;
        } catch (error) {
            if (error.name !== 'AbortError') {
                slotMessage.textContent = 'Az időpontokat most nem sikerült betölteni. Próbáld újra, vagy hívj minket telefonon.';
            }
        }
    }

    form.addEventListener('change', (event) => {
        if (event.target.name === 'service_id' || event.target === dateInput) {
            const chosen = selectedTime();
            slotList.dataset.selected = chosen ? chosen.value : '';
            loadSlots();
        }
    });

    loadSlots();
})();
