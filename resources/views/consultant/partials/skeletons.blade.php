@for($index = 0; $index < ($count ?? 6); $index++)
    <div class="col-md-4">
        <div class="consultant-skeleton-card">
            <div class="d-flex gap-3">
                <div class="consultant-skeleton-avatar"></div>
                <div class="flex-grow-1 pt-1">
                    <div class="consultant-skeleton-line title"></div>
                    <div class="consultant-skeleton-line medium"></div>
                    <div class="consultant-skeleton-badges">
                        <div class="consultant-skeleton-badge"></div>
                        <div class="consultant-skeleton-badge"></div>
                        <div class="consultant-skeleton-badge"></div>
                    </div>
                    <div class="consultant-skeleton-line short"></div>
                    <div class="consultant-skeleton-line medium"></div>
                </div>
            </div>

            <div class="consultant-skeleton-divider"></div>

            <div class="consultant-skeleton-footer">
                <div class="consultant-skeleton-line short" style="margin-bottom: 0; width: 90px;"></div>
                <div class="consultant-skeleton-button"></div>
            </div>
        </div>
    </div>
@endfor
