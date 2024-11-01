ARG NGINX_VERSION

FROM nginx:${NGINX_VERSION}

RUN rm /etc/nginx/conf.d/default.conf

COPY nginx.conf /etc/nginx/nginx.conf
COPY conf.d /etc/nginx/conf.d
COPY templates /etc/nginx/templates
COPY includes /etc/nginx/includes

EXPOSE 80 443 2095 2096