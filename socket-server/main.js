const express = require('express');
const app = express();
const cors = require('cors');
const http = require('http').createServer(app);
const io = require('socket.io')(http, {
    cors: {
        origin: [
	  '80.232.219.79',
	  '80.232.219.79:3000',
	  '80.232.219.79:8081',
	  'http://r1riepas.lv',
	  'https://r1riepas.lv',
	  'http://localhost:3000',
	  'http://localhost:8081',
	],
	methods: ["GET", "POST"]
    }
});
const axios = require('axios');
const bodyParser = require('body-parser');

const LARAVEL_API = 'https://r1riepas.lv';

io.on('connection', (socket) => {
    console.log('Client connected');

    const ip = socket.handshake.headers['x-forwarded-for'] || socket.handshake.address;
    console.log(`Новое подключение: IP = ${ip}`);

    socket.on('new_booking', (booking) => {
        // Отправляем всем подключенным клиентам
        io.emit('new_booking', booking);
    });

    socket.on('disconnect', () => {
        console.log('Client disconnected');
    });
});

app.use(bodyParser.json());
app.use(cors({ origin: "*", credentials: true }));

let csrfToken = null;

// Получение CSRF-токена
async function getCsrfToken(force = false) {
    if (csrfToken && !force) return csrfToken;
    const response = await axios.get(`${LARAVEL_API}/api/csrf-token`);
    csrfToken = response.data.csrf_token;
    return csrfToken;
}

app.get('/api/csrf-token', async (req, res) => {
    try {
        const response = await axios.get(`${LARAVEL_API}/api/csrf-token`);
        res.json(response.data);
    } catch (err) {
        res.status(500).json({ error: err.toString() });
    }
});

app.get('/', async (req, res) => {
    res.send('hello, get api');
});

// Proxy: получить все слоты
app.get('/api/:branch/slots', async (req, res) => {
    try {
        const date = req.query.date;
        let url = `${LARAVEL_API}/api/${req.params.branch}/slots`;
        if (date) url += `?date=${encodeURIComponent(date)}`;
        const response = await axios.get(url);
        res.json(response.data);
    } catch (err) {
        res.status(500).json({ error: err.toString() });
    }
});

// Proxy: получить один слот
app.get('/api/slots/:id', async (req, res) => {
    try {
        const response = await axios.get(`${LARAVEL_API}/api/slots/${req.params.id}`);
        res.json(response.data);
    } catch (err) {
        res.status(500).json({ error: err.toString() });
    }
});

app.get('/api/search/slot', async (req, res) => {
    try {
        const { search } = req.query;
        let url = `${LARAVEL_API}/api/search/slot`;
        if (search) url += `?search=${encodeURIComponent(search)}`;
        const response = await axios.get(url);
        res.json(response.data);
    } catch (err) {
        res.status(500).json({ error: err.toString() });
    }
});

// Proxy: обновить слот с CSRF
app.post('/api/slots/:id/update', async (req, res) => {
	try {
		//const csrfResp = axios.get(`${LARAVEL_API}/api/csrf-token`, { withCredentials: true });
		//console.log(csrfResp, csrfResp.data);
		//const csrfToken = csrfResp.data.csrf_token;
		//console.log(csrfToken);
		//const cookies = csrfResp.headers['set-cookie'];
		//console.log(cookies);

		const response = await axios.post(
			`${LARAVEL_API}/api/slots/${req.params.id}/update`,
			req.body,
			{
				headers: {
					'Content-Type': 'application/json',
				}
			}
		);
		res.json(response.data);
	} catch (err) {
		res.status(500).json({ error: err.toString() });
	}
});

app.get('/api/lift-spots', async (req, res) => {
  try {
    const response = await axios.get(`${LARAVEL_API}/api/lift-spots`);
    res.json(response.data);
  } catch (err) {
    res.status(500).json({ error: err.toString() });
  }
});

app.get('/api/services-list', async (req, res) => {
  try {
    const response = await axios.get(`${LARAVEL_API}/api/services-list`);
    res.json(response.data);
  } catch (err) {
    res.status(500).json({ error: err.toString() });
  }
});

http.listen(3030, () => {
    console.log('Socket.IO server running on port 3030');
});
