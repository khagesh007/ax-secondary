function createPublishStatusBadge(status){
   return    `<span class="badge badge-soft-${status == "PUBLISHED" ? "success" : (status == "DRAFT" ? "primary" : status == "PENDING" ? "warning" : status == "POCKET" ? "secondary": "danger")}">${status}</span>`;
}
