## Parameters

### Global parameters

| Name                  | Description     | Value                                      |
| --------------------- | --------------- | ------------------------------------------ |
| `global.patroni.name` | Name of patroni | `edusharing-services-rendering-postgresql` |

### Local parameters

| Name                                                                    | Description                                                       | Value                                                             |
| ----------------------------------------------------------------------- | ----------------------------------------------------------------- | ----------------------------------------------------------------- |
| `nameOverride`                                                          | Override name                                                     | `edusharing-services-rendering`                                   |
| `edusharing_services_rendering_postgresql.enabled`                      | Enable postgresql rendering service                               | `true`                                                            |
| `edusharing_services_rendering_postgresql.image.name`                   | Set postgresql rendering service image name                       | `${docker.edu_sharing.community.common.postgresql.name}`          |
| `edusharing_services_rendering_postgresql.image.tag`                    | Set postgresql rendering service image tag                        | `${docker.edu_sharing.community.common.postgresql.tag}`           |
| `edusharing_services_rendering_postgresql.nameOverride`                 | Override edusharing postgresql rendering service name             | `edusharing-services-rendering-postgresql`                        |
| `edusharing_services_rendering_postgresql.service.port.api`             | Set port for postgresql rendering service api                     | `5432`                                                            |
| `edusharing_services_rendering_postgresql.config.database`              | Set postgresql rendering service database name                    | `rendering`                                                       |
| `edusharing_services_rendering_postgresql.config.username`              | Set postgresql rendering service database username                | `rendering`                                                       |
| `edusharing_services_rendering_postgresql.init.permission.image.name`   | Set postgresql rendering service init permission container name   | `${docker.edu_sharing.community.common.minideb.name}`             |
| `edusharing_services_rendering_postgresql.init.permission.image.tag`    | Set postgresql rendering service init permission container tag    | `${docker.edu_sharing.community.common.minideb.tag}`              |
| `edusharing_services_rendering_postgresql.job.dump.image.name`          | Set postgresql rendering service dump job container name          | `${docker.edu_sharing.community.common.postgresql.name}`          |
| `edusharing_services_rendering_postgresql.job.dump.image.tag`           | Set postgresql rendering service dump job container tag           | `${docker.edu_sharing.community.common.postgresql.tag}`           |
| `edusharing_services_rendering_postgresql.sidecar.metrics.image.name`   | Set postgresql rendering service metrics sidecar name             | `${docker.edu_sharing.community.common.postgresql.exporter.name}` |
| `edusharing_services_rendering_postgresql.sidecar.metrics.image.tag`    | Set postgresql rendering service metrics sidecar tag              | `${docker.edu_sharing.community.common.postgresql.exporter.tag}`  |
| `edusharing_services_rendering_rediscluster.enabled`                    | Enable rediscluster rendering service                             | `true`                                                            |
| `edusharing_services_rendering_rediscluster.image.name`                 | Set rediscluster rendering service image name                     | `${docker.edu_sharing.community.common.redis-cluster.name}`       |
| `edusharing_services_rendering_rediscluster.image.tag`                  | Set rediscluster rendering service image tag                      | `${docker.edu_sharing.community.common.redis-cluster.tag}`        |
| `edusharing_services_rendering_rediscluster.nameOverride`               | Override edusharing rediscluster rendering service name           | `edusharing-services-rendering-rediscluster`                      |
| `edusharing_services_rendering_rediscluster.service.port.api`           | Set port for rediscluster rendering service api                   | `6379`                                                            |
| `edusharing_services_rendering_rediscluster.init.permission.image.name` | Set rediscluster rendering service init permission container name | `${docker.edu_sharing.community.common.minideb.name}`             |
| `edusharing_services_rendering_rediscluster.init.permission.image.tag`  | Set rediscluster rendering service init permission container tag  | `${docker.edu_sharing.community.common.minideb.tag}`              |
| `edusharing_services_rendering_rediscluster.init.sysctl.image.name`     | Set rediscluster rendering service init sysctl container name     | `${docker.edu_sharing.community.common.minideb.name}`             |
| `edusharing_services_rendering_rediscluster.init.sysctl.image.tag`      | Set rediscluster rendering service init sysctl container tag      | `${docker.edu_sharing.community.common.minideb.tag}`              |
| `edusharing_services_rendering_rediscluster.sidecar.metrics.image.name` | Set rediscluster rendering service metrics sidecar name           | `${docker.edu_sharing.community.common.redis.exporter.name}`      |
| `edusharing_services_rendering_rediscluster.sidecar.metrics.image.tag`  | Set rediscluster rendering service metrics sidecar tag            | `${docker.edu_sharing.community.common.redis.exporter.tag}`       |
| `edusharing_services_rendering_service.enabled`                         | Enable rendering service                                          | `true`                                                            |
| `edusharing_services_rendering_service.config.cache.host`               | Set rendering service cache host                                  | `edusharing-services-rendering-rediscluster`                      |
| `edusharing_services_rendering_service.config.cache.port`               | Set rendering service cache port                                  | `6379`                                                            |
| `edusharing_services_rendering_service.config.database.host`            | Set rendering service database host                               | `edusharing-services-rendering-postgresql`                        |
| `edusharing_services_rendering_service.config.database.port`            | Set rendering service database port                               | `5432`                                                            |
| `edusharing_services_rendering_service.config.database.database`        | Set rendering service database name                               | `rendering`                                                       |
| `edusharing_services_rendering_service.config.database.username`        | Set rendering service database username                           | `rendering`                                                       |
