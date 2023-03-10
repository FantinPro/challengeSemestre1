export const createReport = async ({ messageId, userId, type }) => {
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
      type,
    }),
  });
  const json = await response.json();
  if (!response.ok) {
    throw new Error(json.detail);
  }
  return json;
};

export const getAllMessagesWithAtLeast2Reports = async (page, isDeleted, { orderBy = 'created', order = 'asc' } = {}) => {
  const response = await fetch(
    `${import.meta.env.VITE_API_URL}/api/messages/reports?page=${page}&isDeleted=${isDeleted}&order[${orderBy}]=${order}`,
    {
      method: 'GET',
      headers: {
        // Accept: 'application/json',
        'Content-Type': 'application/json',
        Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
      },
    }
  );
  const json = await response.json();
  if (!response.ok) {
    throw new Error(json.detail);
  }
  const { 'hydra:member': messagesWithReports, 'hydra:totalItems': total } =
    json;
  return { messagesWithReports, total };
};

export const rejectReports = async ({ messageId }) => {
  const response = await fetch(
    `${
      import.meta.env.VITE_API_URL
    }/api/messages/reports/reject?messageId=${messageId}`,
    {
      method: 'DELETE',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        Authorization: `Bearer ${$cookies.get('echo_user_token')}`,
      },
    }
  );
  if (!response.ok) {
    throw new Error('something went wrong');
  }
  return {};
};
