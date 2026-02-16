import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  scenarios: {
    infra_test: {
      executor: 'ramping-vus',
      startVUs: 0,
      stages: [
        { duration: '30s', target: 2 },
        { duration: '1m', target: 5 },
        { duration: '1m', target: 10 },
        { duration: '1m', target: 20 },
        { duration: '1m', target: 0 },
      ],
      gracefulRampDown: '30s',
    },
    functional_test: {
      executor: 'ramping-vus',
      startVUs: 0,
      stages: [
        { duration: '30s', target: 1 },
        { duration: '1m', target: 2 },
        { duration: '1m', target: 3 },
        { duration: '1m', target: 0 },
      ],
      gracefulRampDown: '30s',
      exec: 'downloadTest',
    },
  },
};

// Scenario 1: infrastructure load test
export default function () {
  const res = http.get('https://reelsnap.onrender.com/api/load-test');
  check(res, { 'status is 200': (r) => r.status === 200 });
  sleep(1);
}

// Scenario 2: functional download test
export function downloadTest() {
  const payload = { url: 'https://www.instagram.com/reel/DU6HoHRCppE' };
  const params = { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } };

  const res = http.post('https://reelsnap.onrender.com/api/download-test', payload, params);

  check(res, {
    'status is 200': (r) => r.status === 200,
    'response has download_url': (r) => r.body.includes('download_url')
  });

  sleep(1);
}