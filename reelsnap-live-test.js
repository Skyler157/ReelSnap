import http from 'k6/http';
import { check, sleep } from 'k6';

const BASE_URL = (__ENV.BASE_URL || 'https://reelsnap.onrender.com').replace(/\/+$/, '');
const REEL_URL = __ENV.REEL_URL || 'https://www.instagram.com/reel/DU6HoHRCppE/';

export const options = {
  scenarios: {
    health_check: {
      executor: 'ramping-vus',
      startVUs: 1,
      stages: [
        { duration: '20s', target: 3 },
        { duration: '40s', target: 5 },
        { duration: '20s', target: 0 },
      ],
      gracefulRampDown: '10s',
      exec: 'healthScenario',
    },
    homepage_check: {
      executor: 'ramping-vus',
      startVUs: 1,
      stages: [
        { duration: '20s', target: 2 },
        { duration: '40s', target: 4 },
        { duration: '20s', target: 0 },
      ],
      gracefulRampDown: '10s',
      exec: 'homepageScenario',
    },
    api_contract_check: {
      executor: 'ramping-vus',
      startVUs: 1,
      stages: [
        { duration: '20s', target: 2 },
        { duration: '40s', target: 4 },
        { duration: '20s', target: 0 },
      ],
      gracefulRampDown: '10s',
      exec: 'apiContractScenario',
    },
    web_form_check: {
      // Keep this low to avoid hitting throttle:10,1 on /download
      executor: 'shared-iterations',
      vus: 1,
      iterations: 6,
      maxDuration: '3m',
      exec: 'webFormScenario',
    },
  },
  thresholds: {
    http_req_failed: ['rate<0.05'],
    checks: ['rate>0.95'],
    'http_req_duration{scenario:health_check}': ['p(95)<1500'],
    'http_req_duration{scenario:homepage_check}': ['p(95)<4000'],
    'http_req_duration{scenario:api_contract_check}': ['p(95)<2500'],
    'http_req_duration{scenario:web_form_check}': ['p(95)<8000'],
  },
};

function extractCsrfToken(html) {
  const tokenField = html.match(/name="_token"\s+value="([^"]+)"/i);
  if (tokenField && tokenField[1]) {
    return tokenField[1];
  }

  const reverseOrder = html.match(/value="([^"]+)"\s+name="_token"/i);
  return reverseOrder ? reverseOrder[1] : null;
}

export function healthScenario() {
  const res = http.get(`${BASE_URL}/up`, { tags: { name: 'health_up' } });

  check(res, {
    'health /up is 200': (r) => r.status === 200,
  });

  sleep(1);
}

export function homepageScenario() {
  const res = http.get(`${BASE_URL}/`, { tags: { name: 'home' } });

  check(res, {
    'home is 200': (r) => r.status === 200,
    'home contains ReelSnap': (r) => r.body && r.body.includes('ReelSnap'),
  });

  sleep(1);
}

export function apiContractScenario() {
  const loadRes = http.get(`${BASE_URL}/api/load-test`, { tags: { name: 'api_load_test' } });
  check(loadRes, {
    'api/load-test is 200': (r) => r.status === 200,
    'api/load-test has status=ok': (r) => r.body && r.body.includes('"status":"ok"'),
  });

  const downloadRes = http.post(`${BASE_URL}/api/download-test`, null, {
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    tags: { name: 'api_download_test' },
  });

  check(downloadRes, {
    'api/download-test is 200': (r) => r.status === 200,
    'api/download-test has download_url': (r) => r.body && r.body.includes('download_url'),
  });

  sleep(1);
}

export function webFormScenario() {
  const pageRes = http.get(`${BASE_URL}/`, { tags: { name: 'web_get_home_for_csrf' } });

  const token = extractCsrfToken(pageRes.body || '');

  check(pageRes, {
    'csrf page is 200': (r) => r.status === 200,
    'csrf token found': () => token !== null,
  });

  if (!token) {
    sleep(10);
    return;
  }

  const formPayload = {
    _token: token,
    url: REEL_URL,
  };

  // Do not auto-follow redirects so we can assert route behavior directly.
  const postRes = http.post(`${BASE_URL}/download`, formPayload, {
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    redirects: 0,
    tags: { name: 'web_download_submit' },
  });

  check(postRes, {
    'download submit not 500': (r) => r.status !== 500,
    'download submit expected status': (r) => [302, 303, 422, 429].includes(r.status),
  });

  // Keep below throttle window pressure.
  sleep(10);
}
