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
  const json = await response.json();
  if (!response.ok) {
    throw new Error(json.detail);
  }
  return json;
};
  