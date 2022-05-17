const http = require('k6/http')
const {sleep} = require('k6')

export let options = {
    stages: [
        {duration: '40s', target: 20},
        {duration: '40s', target: 50},
        {duration: '40s', target: 100},
        {duration: '40s', target: 200},
        {duration: '40s', target: 300},
        {duration: '40s', target: 500},
        {duration: '40s', target: 500},
        {duration: '30s', target: 500},
        {duration: '30s', target: 600},
        {duration: '30s', target: 800},
        {duration: '30s', target: 800},
        {duration: '30s', target: 800},
        {duration: '30s', target: 800},
        {duration: '30s', target: 1000},
        {duration: '30s', target: 1000},
        {duration: '30s', target: 1000},
        {duration: '30s', target: 1200},
        {duration: '30s', target: 1200},
        {duration: '30s', target: 1200},
        {duration: '30s', target: 1500},
        {duration: '30s', target: 1500},
        {duration: '30s', target: 1500},
        {duration: '30s', target: 1500},
        {duration: '30s', target: 1500},
        {duration: '30s', target: 1700},
        {duration: '30s', target: 1700},
        {duration: '30s', target: 1700},
        {duration: '30s', target: 1700},
        {duration: '30s', target: 1700},
        {duration: '30s', target: 1500},
        {duration: '30s', target: 1000},
        {duration: '30s', target: 1000},
        {duration: '30s', target: 1000},
        {duration: '40s', target: 500},
        {duration: '40s', target: 500},
        {duration: '40s', target: 500},
        {duration: '40s', target: 500},
        {duration: '30s', target: 300},
        {duration: '30s', target: 100},
    ],
    thresholds: {
        http_req_duration: ['p(99)<60'], // 99% of requests must complete below 1.5s
    },
};

const BASE_URL = 'invoice-manager.test';

export default () => {
    const payload = JSON.stringify({
        'employer_id': 123,
        'worker_id': 321,
        'insurance': 20.2,
        'shift_price': 768.7,
        'temper_promotion': 10,
        'commission_percentage': 20,
        'late_fees_per_min': 10,
        'over_time_per_min': 20,
        'tax_percentage': 9,
        'shift_start_time': '2022-05-17T08:29:55.390983Z',
        'shift_finish_time': '2022-05-17T16:30:18.887299Z',
        'check_in_time': '2022-05-17T08:55:44.249094Z',
        'check_out_time': '2022-05-17T16:41:11.754872Z'
    });

    const params = {
        headers: {
            'Content-Type': 'application/json',
        },
    };

    http.post(`${BASE_URL}/api/internal/calculate-invoice`, payload, params);
    sleep(1);
};
