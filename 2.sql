--- dump базы postgresql

CREATE TABLE public.author (
id SERIAL NOT NULL PRIMARY KEY,
"name" VARCHAR(255) NOT NULL
);

CREATE TABLE public.book (
id SERIAL NOT NULL PRIMARY KEY,
"name" VARCHAR(500) NOT NULL
);

CREATE TABLE public.book_author (
"book_id" INT NOT NULL,
"author_id" INT NOT NULL,
CONSTRAINT pk_book_author PRIMARY KEY (book_id, author_id),
constraint fk_book_author_aurhor_id foreign key (author_id)
                   references "public"."author" (id)
                   on delete CASCADE
                   on update CASCADE,
constraint fk_book_author_books_id foreign key (book_id)
                   references "public"."book" (id)
                   on delete CASCADE
                   on update CASCADE
);



--- данные

INSERT INTO public.author VALUES (2, 'книга 1');
INSERT INTO public.author VALUES (3, 'книга 2');

INSERT INTO public.abook VALUES (2, 'А. С. Пушкин');
INSERT INTO public.abook VALUES (3, 'А. С. Шишкин');

INSERT INTO public.abook_author VALUES (2, 3);
INSERT INTO public.abook_author VALUES (2, 2);
INSERT INTO public.abook_author VALUES (3, 2);



-- запрос на получение данных

SELECT a.name, b.name 
FROM public.book_author ab 
INNER JOIN public.author a ON ab.author_id = a.id
INNER JOIN public.book b ON ab.book_id = b.id
