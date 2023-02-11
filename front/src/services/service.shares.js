export const createShare = async ({ user, message }) => {
  const response = await fetch(
    `${import.meta.env.VITE_API_URL}/api/shares`,
    {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
      },
      body: JSON.stringify({
        sharingBy: `/api/users/${user.id}`,
        sharedMessage: `/api/messages/${message.id}`,
      }),
    }
  );
  const json = await response.json();
  if (!response.ok) {
    throw new Error(json.detail);
  }
  return json;
};

export const deleteShare = async (message) => {
  const response = await fetch(
    `${import.meta.env.VITE_API_URL}/api/shares/delete?messageId=${message.id}`,
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
    throw new Error(json?.detail);
  }
};
    