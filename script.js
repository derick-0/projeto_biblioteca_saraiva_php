// Biblioteca Saraiva - JS integrado com Bootstrap

document.addEventListener('DOMContentLoaded', () => {
  
  const livros = [
    {
      id: 'b1',
      titulo: 'O Pequeno Príncipe',
      autor: 'Antoine de Saint-Exupéry',
      ano: 1943,
      resumo: `Um piloto encontra um pequeno príncipe em um deserto
      e descobre lições sobre amizade, amor e perda.`,
      capa: null,
      disponivel: true
    },
    {
      id: 'b2',
      titulo: 'Dom Casmurro',
      autor: 'Machado de Assis',
      ano: 1899,
      resumo: 'Clássico da literatura brasileira que aborda ciúme e memória.',
      capa: null,
      disponivel: false
    },
    {
      id: 'b3',
      titulo: '1984',
      autor: 'George Orwell',
      ano: 1949,
      resumo: 'Visão distópica de um Estado totalitário que vigia e controla.',
      capa: null,
      disponivel: true
    }
  ];

  
  const bookListEl = document.querySelector('#book-list');
  const searchInput = document.querySelector('#search-input');
  const sortSelect = document.querySelector('#sort-select');

  
  const bookModalEl = document.querySelector('#bookModal');
  const modalTitle = bookModalEl ? bookModalEl.querySelector('.modal-title') : null;
  const modalBody = bookModalEl ? bookModalEl.querySelector('.modal-body') : null;

  
  let filtroTexto = '';
  let modoOrdenacao = 'titulo';
  let listaAtiva = [...livros];

  

  function render() {
    const filtrados = listaAtiva
      .filter(l => {
        if (!filtroTexto) return true;
        const t = filtroTexto.toLowerCase();
        return (
          l.titulo.toLowerCase().includes(t) ||
          l.autor.toLowerCase().includes(t) ||
          String(l.ano).includes(t)
        );
      })
      .sort((a, b) => {
        if (modoOrdenacao === 'titulo') return a.titulo.localeCompare(b.titulo);
        if (modoOrdenacao === 'autor') return a.autor.localeCompare(b.autor);
        if (modoOrdenacao === 'ano') return a.ano - b.ano;
        return 0;
      });

    if (!bookListEl) {
      console.warn('Elemento #book-list não encontrado no HTML.');
      return;
    }

    bookListEl.innerHTML = '';

    if (filtrados.length === 0) {
      bookListEl.innerHTML = '<p class="text-muted">Nenhum livro encontrado.</p>';
      return;
    }

    filtrados.forEach(livro => {
      const card = renderBook(livro);
      bookListEl.appendChild(card);
    });
  }

  function renderBook(l) {
    const col = document.createElement('div');
    col.className = 'col-12 col-sm-6 col-md-4 mb-3';

    const card = document.createElement('div');
    card.className = 'card h-100';

    const capa = document.createElement('img');
    capa.className = 'card-img-top';
    capa.alt = `Capa de ${l.titulo}`;
    capa.style.objectFit = 'cover';
    capa.style.height = '220px';
    capa.src = l.capa || 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="400" height="220"><rect width="100%" height="100%" fill="%23e9ecef"/><text x="50%" y="50%" font-size="18" fill="%236c757d" dominant-baseline="middle" text-anchor="middle">Capa indisponível</text></svg>';

    const body = document.createElement('div');
    body.className = 'card-body d-flex flex-column';

    const title = document.createElement('h5');
    title.className = 'card-title';
    title.textContent = l.titulo;

    const author = document.createElement('p');
    author.className = 'card-text mb-1 text-muted';
    author.textContent = `${l.autor} • ${l.ano}`;

    const resumo = document.createElement('p');
    resumo.className = 'card-text flex-grow-1';
    resumo.textContent =
      l.resumo.length > 140 ? l.resumo.slice(0, 140) + '...' : l.resumo;

    const footer = document.createElement('div');
    footer.className = 'mt-2 d-flex justify-content-between align-items-center';

    const detalhesBtn = document.createElement('button');
    detalhesBtn.className = 'btn btn-sm btn-outline-primary';
    detalhesBtn.textContent = 'Detalhes';
    detalhesBtn.addEventListener('click', () => abrirModalDetalhes(l));

    const emprestarBtn = document.createElement('button');
    emprestarBtn.className = 'btn btn-sm';
    emprestarBtn.textContent = l.disponivel ? 'Emprestar' : 'Reservar';
    emprestarBtn.classList.add(l.disponivel ? 'btn-success' : 'btn-warning');
    emprestarBtn.disabled = !l.disponivel && false;
    emprestarBtn.addEventListener('click', () => emprestarLivro(l.id));

    footer.appendChild(detalhesBtn);
    footer.appendChild(emprestarBtn);

    body.appendChild(title);
    body.appendChild(author);
    body.appendChild(resumo);
    body.appendChild(footer);

    card.appendChild(capa);
    card.appendChild(body);
    col.appendChild(card);

    return col;
  }

  

  function abrirModalDetalhes(livro) {
    if (!bookModalEl) {
      alert(
        `${livro.titulo} —\nAutor: ${livro.autor}\nAno: ${livro.ano}\n\n${livro.resumo}`
      );
      return;
    }

    modalTitle.textContent = livro.titulo;
    modalBody.innerHTML = `
      <p><strong>Autor:</strong> ${livro.autor}</p>
      <p><strong>Ano:</strong> ${livro.ano}</p>
      <p>${livro.resumo}</p>
      <p><small>ID: ${livro.id} • ${
      livro.disponivel ? 'Disponível' : 'Indisponível'
    }</small></p>
    `;

    const modal = new bootstrap.Modal(bookModalEl);
    modal.show();
  }

  function emprestarLivro(bookId) {
    const idx = listaAtiva.findIndex(b => b.id === bookId);
    if (idx === -1) return alert('Livro não encontrado.');

    if (!listaAtiva[idx].disponivel) {
      alert('Livro indisponível — você pode reservar.');
      return;
    }

    listaAtiva[idx].disponivel = false;
    render();

    const toastEl = document.createElement('div');
    toastEl.className = 'toast align-items-center text-bg-success border-0';
    toastEl.setAttribute('role', 'status');
    toastEl.setAttribute('aria-live', 'polite');
    toastEl.setAttribute('aria-atomic', 'true');

    toastEl.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">Livro emprestado com sucesso!</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    `;

    document.body.appendChild(toastEl);

    const toast = new bootstrap.Toast(toastEl, { delay: 2500 });
    toast.show();

    setTimeout(() => toastEl.remove(), 3000);
  }

  // Eventos UI
  if (searchInput) {
    searchInput.addEventListener('input', e => {
      filtroTexto = e.target.value;
      render();
    });
  }

  if (sortSelect) {
    sortSelect.addEventListener('change', e => {
      modoOrdenacao = e.target.value;
      render();
    });
  }

  
  window._bibliotecaSaraiva = {
    livros: listaAtiva,
    render,
    abrirModalDetalhes,
    emprestarLivro
  };

  
  render();
});
