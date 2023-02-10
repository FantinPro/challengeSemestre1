export const getStats = async ({ startDate, endDate }) => {
  let params = '';
  if (startDate && endDate) {
    params = `?startDate=${startDate.toISOString()}&endDate=${endDate.toISOString()}`;
  }
  const response = await fetch(
    `${import.meta.env.VITE_API_URL}/api/stats${params}`,
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
    throw new Error('Something went wrong');
  }
  return json;
};
  