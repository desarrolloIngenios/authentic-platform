# Multi-stage build for Cloud SQL Proxy
# This ensures we get the correct architecture for our GKE cluster

# Stage 1: Get the official Cloud SQL Proxy binary
FROM --platform=linux/amd64 gcr.io/cloud-sql-connectors/cloud-sql-proxy:2.8.0@sha256:9c84401d9c31d18809b02155e74920d0434a7d8780d2b63b8de7a690fea6f1bf AS proxy-source

# Stage 2: Create our custom image with the correct binary
FROM --platform=linux/amd64 gcr.io/distroless/base-debian12:latest

# Copy the Cloud SQL Proxy binary from the official image
COPY --from=proxy-source /cloud-sql-proxy /cloud-sql-proxy

# Set the binary as executable
USER 65532:65532
ENTRYPOINT ["/cloud-sql-proxy"]