
  <div class="container mt-5">
    <h2 class="mb-4"><i class="fa-regular fa-star-half-stroke mr-1"></i>{{ __('lang.customer_feedback') }}</h2>

    <!-- Search -->
    <div class="form-group">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text" id="search-icon">
            <i class="fas fa-search"></i>
          </span>
        </div>
        <input
          type="text"
          id="searchInput"
          class="form-control"
          placeholder="{{ __('lang.search_feedback_placeholder') }}"
          aria-label="{{ __('lang.search_feedback_placeholder') }}"
          aria-describedby="search-icon"
        />
      </div>
    </div>

    <!-- Feedback List -->
    <div id="feedbackList">
      
    </div>

    <!-- Pagination Controls -->
    <div class="d-flex justify-content-between align-items-center mt-5">
      <div class="col-sm-4">
        <label>{{ __('lang.show') }}:</label>
        <select id="itemsPerPage" class="form-control d-inline-block w-auto">
          <option>5</option>
          <option>10</option>
          <option>15</option>
          <option>20</option>
        </select>
      </div>
      <nav>
        <ul id="pagination" class="pagination col-sm-8 mb-0"></ul>
      </nav>
    </div>
  </div>

