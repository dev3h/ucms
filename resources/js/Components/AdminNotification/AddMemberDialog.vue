<template>
    <el-dialog
        v-model="isShowDialog"
        title="Choose user to send notification"
        width="800px"
        top="50px"
        :close-on-click-modal="false"
        class="add-member-dialog"
    >
        <div class="min-h-[40vh]">
            <div class="flex gap-[16px]">
                <el-input
                    v-model="filters.name"
                    size="large" placeholder="enter name"
                    class="w-full" clearable
                    @keypress.enter.prevent="searchData()"
                />
                <el-button type="primary" size="large" class="btn-basic" @click="searchData()">Search</el-button>
            </div>
            <div class="h-full">
                <div class="mt-3">
                    <div class="mb-[4px]">Selected: </div>
                    <div v-if="userSelects.length > 0" class="mt-2">
                        <div class="grid grid-cols-4 gap-x-[14px] gap-y-[6px] px-[12px]">
                            <div
                                v-for="(item, index) in userSelects" :key="index"
                                class="bg-[#F5F5F5] py-[6px] px-[12px] rounded-[12px] flex justify-between items-center gap-y-[8px]"
                            >
                                <div class="w-[90%]" :title="item?.name">
                                    <div class="flex items-center gap-[8px]">
<!--                                        <el-avatar :size="28" :src="item.avatar" />-->
                                        <div class="single-line-text flex-1">{{ item?.name }}</div>
                                    </div>
                                </div>
                                <div class="cursor-pointer" @click="removeMember(index, item)">
                                    <img src="/images/svg/remove-member.svg" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="mt-2 w-full text-center py-[4px]">No selected</div>
                </div>
                <div v-loading="loadingMore">
                    <el-main class="!pt-0 !px-0 mt-3 max-h-[500px] border-t-[2px] border-[#DCDFE6] overflow-y-scroll">
                        <ul
                            v-if="listUsers.length > 0"
                            v-infinite-scroll="loadMore"
                            class="list"
                        >
                            <div
                                v-for="(item, index) in listUsers" :key="index"
                                class="flex items-center px-[24px] py-[12px] gap-[8px] border-b-[1px] cursor-pointer hover:bg-[#f5f5f5]"
                                @click="toggleSelect($event, index)"
                            >
                                <div class="w-[40px]">
                                    <el-checkbox :id="'check_' + index" v-model="item.is_select" size="large" @change="changeMember($event, item)"/>
                                </div>
                                <div class="flex-1 !text-[#000]">
                                    <div class="flex items-center gap-[8px]">
<!--                                        <el-avatar :size="35" :src="item.avatar" />-->
                                        <div class="flex-1">{{ item.name }}</div>
                                    </div>
                                </div>
                            </div>
                        </ul>
                        <el-empty v-else description="No data" />
                    </el-main>
                </div>
            </div>
        </div>
        <template #footer>
            <span class="dialog-footer flex justify-center items-center">
                <el-button
                    type="info" size="large"
                    class="button-min--width"
                    @click="closeForm"
                >
                    Cancel
                </el-button>
                <el-button
                    type="primary" size="large"
                    :disabled="userSelects.length == 0"
                    class="btn-basic button-min--width"
                    @click="submit()"
                >
                    Add
                </el-button>
            </span>
        </template>
    </el-dialog>
</template>

<script>
import axios from '@/Plugins/axios'
import DataTable from '@/Components/Page/DataTable.vue'

export default {
    name: 'AddMemberDialog',
    components: { DataTable },
    props: {
        noticeId: { type: String, required: false },
    },
    emits: ['submit'],
    data() {
        return {
            loadingMore: false,
            isShowDialog: false,
            filters: {
                nickname: null,
                page: 0,
            },
            paginate: {
                last_page: 1
            },
            listUsers: [],
            userSelecteds: [],
            userSelects: [],
        }
    },
    watch: {
        isShowDialog(val) {
            if (val == false) {
                this.filters.nickname = null
                this.filters.page = 0
                this.paginate = {}
                this.listUsers = []
                this.userSelecteds = []
                this.userSelects = []
            }
        }
    },
    methods: {
        open(userIds) {
            this.isShowDialog = true
            this.userSelecteds = userIds
            this.fetchData()
        },
        async fetchData() {
            this.filters.page += 1
            if (this.filters.page > this.paginate.last_page) return
            this.loadingMore = true
            let params = {
                notice_id: this.noticeId,
                nickname: this.filters.nickname,
                user_ids: this.userSelecteds,
                ...this.filters
            }
            await axios.post(this.appRoute('admin.api.notification.member-can-add'), params)
                .then(({ data }) => {
                    let listUsers = data.data
                    for (let user of listUsers) {
                        let findUserSelect = this.userSelects.find(function(userSelect) {
                            return userSelect.id == user.id
                        })
                        if (findUserSelect) {
                            user.is_select = true
                        } else {
                            user.is_select = false
                        }
                    }

                    if (this.filters.page == 1) {
                        this.listUsers = data.data
                    } else {
                        this.listUsers = [...this.listUsers, ...listUsers]
                    }
                    this.paginate = data?.meta
                })
            this.loadingMore = false
        },
        loadMore() {
            this.fetchData()
        },
        searchData() {
            this.filters.page = 0
            this.fetchData()
        },
        submit() {
            this.$emit('add-member', this.userSelects)
            this.isShowDialog = false
        },
        closeForm() {
            this.isShowDialog = false
        },
        changeMember(event, item) {
            if (event) {
                this.userSelects.push(item)
            } else {
                let findIndex = this.userSelects.findIndex(function(user) {
                    return user.id == item.id;
                });
                this.userSelects.splice(findIndex, 1)
            }
        },
        removeMember(index, item) {
            let userFind = this.listUsers.find(function(user) {
                return user.id == item.id
            })
            if (userFind) {
                userFind.is_select = false
            }
            this.userSelects.splice(index, 1)
        },
        toggleSelect(event, index) {
            if (event.target.classList.contains('el-checkbox__inner')) return;

            let ul = document.querySelector('#check_' + index)
            ul.click()
        }
    },
}
</script>
<style>
.add-member-dialog {
    padding: 0 !important;
}
.add-member-dialog .el-dialog__header {
    border-bottom: 1px solid #ccc;
    padding: 18px 32px !important;
    position: relative !important;
}
.add-member-dialog  .el-dialog__headerbtn {
    top: 50% !important;
    transform: translateY(-50%) !important;
    margin-top: 0 !important;
}
.add-member-dialog .el-dialog__body {
    padding: 18px 32px !important;
}
.add-member-dialog .el-dialog__footer {
    border-top: 1px solid #ccc;
    padding: 18px 24px;
}
.add-member-dialog .single-line-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: calc(100% - 20px);
}
</style>
