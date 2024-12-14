<template>
    <div>
        <h1>Hello, Vue 3!</h1>
        <table class="min-w-full divide-y divide-gray-200 mt-4">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" @click="toggleAll">
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        STT
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nội dung
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(record, index) in paginatedRecords" :key="index">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" :value="record.id" v-model="selectedRecords">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ index + 1 + (currentPage - 1) * recordsPerPage }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ record.content }}
                    </td>
                </tr>
            </tbody>
        </table>

        
        <ul>
            <li v-for="page in totalPages" :key="page" @click="changePage(page)">
                {{ page }}
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: 'HelloWorld',
    data() {
        return {
            currentPage: 1,
            totalRecords: parseInt(document.getElementById('app').dataset.totalRecords, 10), // Lấy dữ liệu từ thuộc tính HTML
            recordsPerPage: 5, // Number of records per page
        };
    },
    computed: {
        totalPages() {
            return Math.ceil(this.totalRecords / this.recordsPerPage);
        },
    },
    methods: {
        changePage(page) {
            this.currentPage = page;
            // Add your logic to fetch data for the selected page
        },
    },
};
</script>

<style scoped>
h1 {
    color: #42b983;
}
ul {
    list-style-type: none;
    padding: 0;
}
li {
    display: inline;
    margin: 0 5px;
    cursor: pointer;
}
li:hover {
    text-decoration: underline;
}
</style>