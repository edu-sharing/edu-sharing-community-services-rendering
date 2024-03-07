{{- define "edusharing_services_rendering.pvc.share.config" -}}
share-config-{{ include "edusharing_common_lib.names.name" . }}
{{- end -}}

{{- define "edusharing_services_rendering.pvc.share.data" -}}
share-data-{{ include "edusharing_common_lib.names.name" . }}
{{- end -}}
