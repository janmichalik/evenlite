declare const wpData: { ajaxurl: string };
export async function fetchPosts(): Promise<void> {
  const container = document.getElementById('post-container');

  try {
    if (container) { container.innerHTML = 'Ładowanie...' };
    const response = await fetch(wpData.ajaxurl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({ action: 'get_posts_list' }),
    });

    if (!response.ok) {
      throw new Error(`Błąd HTTP: ${response.status}`);
    }

    const data = await response.json();

    if (container) {
      container.innerHTML = data.map((post: any) => `
        <p><a href="${post.link}">${post.title}</a></p>
      `).join('');
    }
  } catch (error) {
    console.error('Błąd fetch:', error);
  }
}
