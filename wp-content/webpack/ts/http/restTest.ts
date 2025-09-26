interface Post {
  userId: number;
  id: number;
  title: string;
  body: string;
}

export async function restTest(): Promise<void> {
  try {
    const response = await fetch('https://jsonplaceholder.typicode.com/posts/1');
    const data: Post = await response.json();
    console.log(`Tytuł posta: ${data.title}`);
  } catch (err) {
    console.error('Coś poszło nie tak:', err);
  }
}
