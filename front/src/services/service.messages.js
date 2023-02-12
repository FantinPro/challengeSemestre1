/* eslint-disable no-undef */
export const maskMessage = async ({ messageId }) => {
  const response = await fetch(
    `${import.meta.env.VITE_API_URL}/api/messages/${messageId}`,
    {
      method: 'PATCH',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
      },
      body: JSON.stringify({
        isDeleted: true,
      }),
    }
  );
  const json = await response.json();
  if (!response.ok) {
    throw new Error(json.detail);
  }
  return json;
};

export const fetchFeed = async (page) => {
  const response = await fetch(`${import.meta.env.VITE_API_URL}/api/messages/feed/v2?page=${page}`, {
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
    },
  });
    
  const json = await response.json();
  if (!response.ok) {
    throw new Error(json.detail);
  }
  return json;
}

export const fetchMessage = async (messageId) => {
  const response = await fetch(`${import.meta.env.VITE_API_URL}/api/messages/${messageId}`, {
    method: 'GET',
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
    },
  });
  const json = await response.json();
  if (!response.ok) {
    throw new Error(json.detail);
  }
  return json;
};
