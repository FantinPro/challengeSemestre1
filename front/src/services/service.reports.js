export const createReport = async ({
  messageId,
  userId,
  type
}) => {
  const response = await fetch(`${import.meta.env.VITE_API_URL}/api/reports`, {
    method: 'POST',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
    },
    body: JSON.stringify({
      reportingUser: `/api/users/${userId}`,
      reportedMessage: `/api/messages/${messageId}`,
      type
    }),
  })
  const json = await response.json()
  if (!response.ok) {
    throw new Error(json.detail)
  }
  return json
}