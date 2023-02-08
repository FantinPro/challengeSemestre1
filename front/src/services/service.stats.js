export const getStats = async ({ startDate, endDate }) => {
  const response = await fetch(
    `${import.meta.env.VITE_API_URL}/api/stats?startDate=${startDate.toISOString()}&endDate=${endDate.toISOString()}`,
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
  