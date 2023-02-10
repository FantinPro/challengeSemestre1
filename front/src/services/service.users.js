/* eslint-disable no-undef */
export const changeRoleByUserId = async ({
  userId,
  role,
}) => {
  const response = await fetch(`${import.meta.env.VITE_API_URL}/api/users/${userId}/change_role`, {
    method: 'PUT',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
    },
    body: JSON.stringify({
      roles: [role]
    }),
  })
  const json = await response.json()
  if (!response.ok) {
    throw new Error(json.detail)
  }
  return json
}
