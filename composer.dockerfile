FROM composer:2.8.5

ENV COMPOSERUSER=book_library_system
ENV COMPOSERGROUP=book_library_system

RUN adduser -g ${COMPOSERGROUP} -s /bin/sh -D ${COMPOSERUSER}
