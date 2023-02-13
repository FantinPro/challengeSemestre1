export const getRandomAd = async () => {
  const response = await fetch(
    `${import.meta.env.VITE_API_URL}/api/pubs/random`,
    {
      method: 'GET',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
      },
    }
  );
  if (response.status === 204) {
    console.info('No ads available')
    return null;
  }
  const json = await response.json();
  if (!response.ok) {
    throw new Error(json.detail);
  }
  return json;
};

export const createAd = async ({
  startDate,
  endDate,
  message,
  price,
  owner
}) => {
  const response = await fetch(`${import.meta.env.VITE_API_URL}/api/pubs`, {
    method: 'POST',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
    },
    body: JSON.stringify({
      startDate,
      endDate,
      message,
      price,
      owner
    }),
  });
  const json = await response.json();
  if (!response.ok) {
    throw new Error(json.detail);
  }
  return json;
}

export const getAds = async (page, status, { orderBy = 'created', order = 'asc' } = {}) => {
  const response = await fetch(`${import.meta.env.VITE_API_URL}/api/pubs?page=${page}&status=${status}&order[${orderBy}]=${order}`, {
    method: 'GET',
    headers: {
    //   Accept: 'application/json',
      'Content-Type': 'application/json',
      Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
    },
  });
  const json = await response.json();
  if (!response.ok) {
    throw new Error(json.detail);
  }
  const { 'hydra:member': ads, 'hydra:totalItems': total } =
    json;
  return { ads, total };
}

export const patchAd = async ({ id, status }) => {
  const response = await fetch(`${import.meta.env.VITE_API_URL}/api/pubs/${id}`, {
    method: 'PATCH',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
    },
    body: JSON.stringify({
      status,
    }),
  });
  const json = await response.json();
  if (!response.ok) {
    throw new Error(json.detail);
  }
  return json;
}

export const createImpressionForAd = async ({ ad, fromUser }) => {
  const response = await fetch(`${import.meta.env.VITE_API_URL}/api/stats`, {
    method: 'POST',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
    },
    body: JSON.stringify({
      ad,
      fromUser
    }),
  });
  const json = await response.json();
  if (!response.ok) {
    throw new Error(json.detail);
  }
  return json;
}
  