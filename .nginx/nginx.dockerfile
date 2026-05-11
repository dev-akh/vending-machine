# Use nginx stable image
FROM nginx:stable-alpine

# Set the protocol to either 'http' or 'https'
ARG PROTOCOL=http

# Copy the appropriate Nginx config based on the protocol
# COPY ./default.${PROTOCOL}.conf /etc/nginx/conf.d/default.conf
COPY ./default.http.conf /etc/nginx/conf.d/default.http.conf
# Optional: Copy SSL certs if using https
# COPY ./nginx/certs/ /etc/nginx/certs/mkcert

# Copy other configurations if needed
COPY ./conf.d/ /etc/nginx/conf.d

# Expose the default HTTP port
EXPOSE 80

EXPOSE 443

