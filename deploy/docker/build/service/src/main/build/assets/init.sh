#!/bin/bash
[[ -n $DEBUG ]] && set -x
set -eu

########################################################################################################################

my_host_internal="${SERVICES_RENDERING_SERVICE_HOST_INTERNAL:-services-rendering-service}"
my_port_internal="${SERVICES_RENDERING_SERVICE_PORT_INTERNAL:-8080}"

my_base_internal="http://${my_host_internal}:${my_port_internal}/esrender"
my_meta_internal="${my_base_internal}/application/esmain/metadata.php"

repository_service_host="${REPOSITORY_SERVICE_HOST:-repository-service}"
repository_service_port="${REPOSITORY_SERVICE_PORT:-8080}"

repository_service_base="http://${repository_service_host}:${repository_service_port}/edu-sharing"

repository_service_admin_user="admin"
repository_service_admin_pass="${REPOSITORY_SERVICE_ADMIN_PASS:-admin}"

### Wait ###############################################################################################################

until wait-for-it "${my_host_internal}:${my_port_internal}" -t 3; do sleep 1; done

until [[ $( curl -sSf -w "%{http_code}\n" -o /dev/null "${my_meta_internal}" ) -eq 200 ]]
do
	echo >&2 "Waiting for ${my_host_internal} ..."
	sleep 3
done

until wait-for-it "${repository_service_host}:${repository_service_port}" -t 3; do sleep 1; done

until [[ $(curl -sSf -w "%{http_code}\n" -o /dev/null -H 'Accept: application/json' "${repository_service_base}/rest/_about/status/SERVICE?timeoutSeconds=3") -eq 200 ]]; do
	echo >&2 "Waiting for ${repository_service_host} service ..."
	sleep 3
done

########################################################################################################################

# Upsert the application in a single call: the PUT /applications/xml endpoint parses the appid from the
# uploaded metadata and updates it in place if already registered, otherwise inserts it. No list/delete dance.
curl -sS "${my_meta_internal}" \
  | curl -sS \
      -H "Accept: application/json" \
      --user "${repository_service_admin_user}:${repository_service_admin_pass}" \
      -XPUT \
      -F "xml=@-;type=text/xml;filename=metadata.xml" \
      "${repository_service_base}/rest/admin/v1/applications/xml"

