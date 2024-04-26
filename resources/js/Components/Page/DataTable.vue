<template>
    <div class="DataTable" shadow="never">
        <div class="scroll-wrapper" />
        <el-table
            ref="table"
            :data="items"
            :default-sort="defaultSort || {}"
            empty-text="No data"
            @sort-change="sortChange"
            @selection-change="handleSelectionChange"
            @row-click="rowClick"
        >
            <el-table-column v-if="enableSelectBox" type="selection" width="55" />
            <el-table-column v-if="enableIndex" type="index" fixed width="70" label="No." align="center" />
            <el-table-column
                v-for="(field, index) in fields"
                :key="index"
                :fixed="field.fixed"
                :prop="field.key"
                :sortable="field.sortable || false"
                :label="field.label"
                :align="field.align || `left`"
                :header-align="field.headerAlign || `left`"
                :width="field.width || 'auto'"
                :min-width="field.minWidth || 'auto'"
                :label-class-name="field.lableClassName || `left`"
            >
                <!-- Pass on all scoped slots -->
                <template #default="scope">
                    <slot :name="field.key" v-bind="scope">
                        {{ scope.row[field.key] }}
                    </slot>
                </template>
            </el-table-column>

            <!-- Pass on all named slots -->
            <slot v-for="slot in Object.keys($slots)" :slot-scope="slot" :name="slot" />
            <template #empty>
                <el-empty description="No data" />
            </template>
        </el-table>
        <slot v-if="paginate && items.length" name="after">
            <div v-if="!disablePaginateFooter" class="card-footer" :class="{ 'card-footer--center': footerCenter }">
                <div class="table-pagination flex justify-end items-end">
                    <div v-if="!disableTableInfo && paging.from && paging.to" class="table-showing mr-4 w-[200px]">
                        <span class="text-[#646464] text-sm">
                            {{ `${paging.from}-${paging.to} of ${paging.total} items` }}
                        </span>
                    </div>
                    <div class="flex justify-end items-center w-[100%]">
                        <button
                            :disabled="paging.current_page == 1"
                            class="h-[32px] px-[8px] border-[1px] border-[#DCDFE6] rounded-l-[4px] text-[14px]"
                            :class="{ 'cursor-not-allowed opacity-50': paging.current_page == 1 }"
                            @click="handleCurrentChange(1)"
                        >
                            First
                        </button>
                        <div>
                            <el-pagination
                                v-model:page-size="pageSize"
                                :current-page="Number(paginate.current_page) || 1"
                                :page-sizes="pageSizesOpt"
                                :pager-count="pagerCount"
                                layout="->, prev, pager, next"
                                :total="paginate.total"
                                :background="paginateBackground"
                                :prev-icon="getDArrowLeft"
                                :next-icon="getDArrowRight"
                                @current-change="handleCurrentChange"
                                @size-change="handleSizeChange"
                            />
                        </div>
                        <button
                            :disabled="paging.current_page == paging.last_page"
                            class="h-[32px] px-[8px] border-[1px] border-[#DCDFE6] rounded-r-[4px] text-[14px]"
                            :class="{ 'cursor-not-allowed opacity-50': paging.current_page == paging.last_page }"
                            @click="handleCurrentChange(paging.last_page)"
                        >
                            Last
                        </button>
                    </div>
                </div>
            </div>
        </slot>
    </div>
</template>

<script>
import { DArrowLeft, DArrowRight } from '@element-plus/icons-vue'

const pageSizesOpt = [5, 10, 20, 50, 100]

export default {
    props: {
        fields: { type: Array, default: () => [] },
        items: { type: Array, default: () => [] },
        paginate: { type: Object, default: () => {} },
        enableIndex: { type: Boolean, default: false },
        enableSelectBox: { type: Boolean, default: false },
        disableTableInfo: { type: Boolean, default: false },
        disablePaginateFooter: { type: Boolean, default: false },
        headerCenter: { type: Boolean, default: false },
        footerCenter: { type: Boolean, default: false },
        paginateBackground: { type: Boolean, default: true },
        defaultSort: { type: Object, default: () => {} },
        tableHeight: { type: Number, default: 550 },
        pagerCount: { type: Number },
    },
    emits: ['row-selected', 'page-change', 'size-change', 'sort-change', 'row-click'],

    data() {
        return {
            pageSizesOpt,
            pageSize: Number(this.appRoute().params?.limit || pageSizesOpt[0]),
        }
    },
    computed: {
        paging() {
            // eslint-disable-next-line vue/no-side-effects-in-computed-properties
            this.pageSize = Number(this.paginate.per_page || pageSizesOpt[0])
            return this.paginate
        },
        getDArrowRight() {
            return DArrowRight
        },
        getDArrowLeft() {
            return DArrowLeft
        },
    },

    mounted() {
        let triggerScroll = null
        this.$nextTick(() => {
            this.upClassDataTable()

            const tableEl = this.$el.querySelector('.el-scrollbar__view')
            const element = this.$el.querySelector('.el-table__body')

            if (this.$el.querySelector('.is-scrolling-none') == null) {
                const table = this.$el.querySelector('.el-table')

                const tableWidth = table.offsetWidth ? table.offsetWidth + 'px' : table.style.width
                this.$el.querySelector('.scroll-wrapper').style.width = tableWidth

                this.$el.querySelector('.scroll-wrapper').addEventListener('scroll', (event) => {
                    // prevent infinite trigger scroll
                    if (triggerScroll !== 'table') {
                        triggerScroll = 'top'
                        this.scrollTable(event.currentTarget.scrollLeft)
                    } else {
                        triggerScroll = null
                    }
                })

                tableEl.addEventListener('scroll', (event) => {
                    // prevent infinite trigger scroll
                    if (triggerScroll !== 'top') {
                        triggerScroll = 'table'
                        this.scrollTop(event.currentTarget.scrollLeft)
                        // check add class scroll-left or right
                        const scrollbarWidth = element.offsetWidth - table.offsetWidth
                        if (event.currentTarget.scrollLeft < scrollbarWidth / 2) {
                            table.classList.remove('is-scrolling-right')
                            table.classList.add('is-scrolling-left')
                        } else {
                            table.classList.remove('is-scrolling-left')
                            table.classList.add('is-scrolling-right')
                        }
                    } else {
                        triggerScroll = null
                    }
                })
            }
            let isDown = false
            let startX
            let scrollLeft = 0
            let startY
            let scrollTop = 0
            element.addEventListener('mousedown', (e) => {
                isDown = true
                element.classList.add('active')
                startX = e.pageX - element.offsetLeft
                startY = e.pageY - element.offsetTop
                scrollLeft = tableEl.scrollLeft
                scrollTop = tableEl.scrollTop
            })
            const moveListener = (e) => {
                if (!isDown) return
                e.preventDefault()
                const x = e.pageX - element.offsetLeft
                const y = e.pageY - element.offsetTop
                const walkX = (x - startX) * 3
                const walkY = (y - startY) * 3
                if (walkX !== 0) {
                    tableEl.scrollLeft = scrollLeft - walkX
                }
                if (walkY !== 0) {
                    tableEl.scrollTop = scrollTop - walkY
                }
            }
            element.addEventListener('mousemove', moveListener)
            const upListener = () => {
                isDown = false
                element.classList.remove('active')
            }
            element.addEventListener('mouseup', upListener)
            element.addEventListener('mouseleave', upListener)
        })
    },

    methods: {
        toggleSelection(rows) {
            if (rows) {
                rows.forEach((row) => {
                    this.$refs.table.toggleRowSelection(row)
                })
            } else {
                this.$refs.table.clearSelection()
            }
        },
        handleSelectionChange(selectedItems) {
            this.$emit('row-selected', selectedItems)
        },
        rowClick(row) {
            this.$emit('row-click', row)
        },
        handleCurrentChange(value) {
            this.$emit('page-change', value)
            this.upClassDataTable()
        },
        handleSizeChange(value) {
            this.$emit('size-change', value)
            this.upClassDataTable()
        },
        scrollTop(val) {
            this.$el.querySelector('.el-table__header-wrapper').scrollLeft = val
        },
        scrollTable(val) {
            this.$el.querySelector('.el-table__header-wrapper').scrollLeft = val
        },
        sortChange(column) {
            this.$emit('sort-change', column)
        },
        upClassDataTable() {
            setTimeout(() => {
                const updateClassCss = () => {
                    const table = this.$el.querySelector('.el-table')
                    if (table) {
                        table.classList.remove('is-scrolling-right')
                        table.classList.add('is-scrolling-left')
                        clearInterval(interval)
                    }
                }
                const interval = setInterval(updateClassCss)
            }, 500)
        },
    },
}
</script>

<style>
.DataTable .CardHeader,
.DataTable .CardFooter {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.DataTable .CardHeader {
    margin-bottom: 1rem;
}

.DataTable .CardFooter {
    margin-top: 0.9375rem;
}

.DataTable .CardHeader.CardHeaderCenter,
.DataTable .CardFooter.CardFooterCenter {
    justify-content: end;
}

.DataTable .el-table {
    height: auto !important;
    --el-table-border: none;
    max-height: calc(100vh - 250px);
    overflow: auto;
}

.DataTable .el-table__body-wrapper {
    height: auto !important;
    position: relative;
    overflow: hidden;
}

.DataTable .el-table__header th.el-table__cell {
    background-color: var(--tw-gray59);
    color: var(--tw-white);
}
.DataTable .el-table__row td.el-table__cell {
    color: var(--tw-header-table);
    padding: 18px 10px 18px 16px;
}

.DataTable .el-table--scrollable-x .el-table__body-wrapper {
    overflow-x: auto;
}

.DataTable .el-table--scrollable-y .el-table__body-wrapper {
    overflow-y: auto;
}

.DataTable .el-table .el-table__body.active {
    cursor: grabbing;
}

@media only screen and (max-width: 500px) {
    .number {
        min-width: 18px;
    }
}

.DataTable .el-pagination.is-background .btn-next,
.DataTable .el-pagination.is-background .btn-prev,
.DataTable .el-pagination.is-background .el-pager li {
    /* background-color: var(--tw-gray-300); */
    background-color: #fff;
    font-size: 14px;
    min-width: 2rem;
    height: 2rem;
    line-height: 2rem;
    /* border-radius: 8px; */
    /* margin: 0 2px; */
    margin: 0;
    border: 1px solid #dcdfe6;
}

.DataTable .el-pagination.is-background .btn-next,
.DataTable .el-pagination.is-background .btn-prev {
    /* border: 0; */
    border: 1px solid #dcdfe6;
}
.DataTable .el-pagination.is-background button .el-icon {
    font-size: 15px !important;
}

.DataTable .el-pagination.is-background .btn-next span:not([class*='suffix']),
.DataTable .el-pagination.is-background .btn-prev span:not([class*='suffix']) {
    min-width: 2rem;
    height: 2rem;
    line-height: 2rem;
}

.DataTable .el-pagination.is-background .el-pager li:not(.disabled):hover,
.DataTable .el-pagination.is-background .el-pager li:not(.disabled).active {
    background-color: var(--tw-primary);
    color: var(--tw-white);
}

.DataTable .el-pagination.is-background .el-pager li:not(.is-disabled).is-active {
    background-color: var(--tw-primary);
    color: var(--tw-white);
}

.DataTable .el-card__body {
    padding: 0;
}

.DataTable.is-never-shadow {
    border: none;
}
.el-table thead {
    color: #000;
    /* font-size: 13px; */
}

.el-table__header .el-table__cell {
    padding: 7px 10px 7px 16px;
}
.el-table__header .el-table__cell {
    padding: 7px 10px 7px 16px;
}

.DataTable .el-scrollbar__bar.is-horizontal {
    height: 0px;
}

.DataTable .el-scrollbar__view {
    overflow-x: scroll;
    display: block !important;
}

.DataTable .el-table__body .el-table__cell {
    padding: 10px 0;
    border-bottom: 1px solid #dcdfe6;
}

.DataTable .table-pagination {
    background: var(--tw-white);
    padding: 12px 16px;
}

.DataTable .table-pagination .el-pagination {
    width: 100%;
}

.DataTable .table-pagination .el-pagination .el-pagination__sizes {
    margin-right: auto;
}
</style>
