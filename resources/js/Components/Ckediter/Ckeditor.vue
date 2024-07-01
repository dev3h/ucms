<template>
    <div id="editor">
        <ckeditor :config="editorConfig" :editor="editor" v-model="editorData"></ckeditor>
    </div>
</template>

<script>
import '@ckeditor/ckeditor5-build-classic/build/translations/ja';
import ClassicEditor from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';
import { Essentials } from '@ckeditor/ckeditor5-essentials';
import { Bold, Italic, Underline } from '@ckeditor/ckeditor5-basic-styles';
import { Paragraph } from '@ckeditor/ckeditor5-paragraph';
import { Alignment } from '@ckeditor/ckeditor5-alignment';
import { Font } from '@ckeditor/ckeditor5-font';
import { List, ListProperties } from '@ckeditor/ckeditor5-list';

export default {
    name: 'CkeditorComponent',
    props: {
        contentProp: {
            type: String,
            required: false
        },
    },
    mounted() {
        this.translateTextUI()
    },
    watch: {
        contentProp(newVal) {
            this.editorData = newVal;
        },
        editorData() {
            this.emitData()
        },
    },
    data() {
        return {
            editor: ClassicEditor,
            editorData: this.contentProp ?? '',
            editorConfig: {
                fontSize: {
                    options: [ 9, 11, 13, 'default', 17, 19, 21 ]
                },
                list: {
                    properties: {
                        styles: true,
                        startIndex: true,
                        reversed: true
                    }
                },
                plugins: [
                    Essentials,
                    Bold,
                    Italic,
                    Underline,
                    Paragraph,
                    Alignment,
                    Font,
                    List,
                    ListProperties,
                ],
                toolbar: {
                    items: [
                        'bold', 'italic', 'underline',
                        '|',
                        'alignment:left', 'alignment:center', 'alignment:right', 'alignment:justify',
                        '|',
                        'bulletedList', 'numberedList',
                        '|',
                        'fontSize',
                        '|',
                        'undo', 'redo',
                    ]
                },
                language: 'ja',
                addMainLanguageTranslationsToAllAssets: true,
            },
        };
    },
    methods: {
        translateTextUI() {
            !function(e){const t=e.ja=e.ja||{};t.dictionary=Object.assign(t.dictionary||{}, {
                'Font Size': '両端揃え',
                'Align center': '中央揃え',
                'Default': 'デフォルト',
                'Align right': '右揃え',
                'Align left': '左揃え',
                'Justify': '両端揃え',
                'Document colors': 'ドキュメント背景色',
                'Strikethrough': '取り消し線',
                'Text alignment': '文字揃え',
                'Page break': '改ページ',
                'Find and replace': '検索して置換',
                'Find in text…': 'テキスト内を検索...',
                'Replace with…': 'こちらと置換...',
                'Replace': '置換',
                'Replace all': '全てを置換',
                'Find': '見つける',
                'Match case': 'マッチケース',
                'Whole words only': '単語全体のみ',
                'Tip: Find some text first in order to replace it.': 'ヒント: 置換するには、対象テキストを先に検索してください。',
                'Special characters': '特殊文字',
                'Insert image via URL': '画像URLを挿入',
                'Insert': '挿入',
                'Cell properties' : "セルのプロパティ",
                'Table properties': "テーブルのプロパティ",
                'Border': "罫線",
                'Style': "スタイル",
                'None': "なし",
                'Solid': "1本線",
                'Dotted': '点線',
                'Dashed': "破線",
                'Double': "2本線",
                'Groove': "立体的にくぼんだ線",
                'Ridge': "立体的に隆起した線",
                'Inset': "内側全体がくぼんだ線",
                'Outset': "内側全体が隆起した線",
                'Color': "色",
                'The color is invalid. Try "#FF0000" or "rgb(255,0,0)" or "red".': 'この色は無効です。「#FF0000」、「rgb（255,0,0」または「赤」をお試しください。',
                'Background': '背景',
                'Dimensions': '寸法',
                'Table cell text alignment': 'テーブルセルのテキスト配置',
                'Width': '幅',
                'Height': '高さ',
                'Padding': 'パディング',
                'Align cell text to the left': 'セルのテキストを左へ寄せる',
                'Align cell text to the center': 'セルのテキストを中央へ揃える',
                'Align cell text to the right': 'セルのテキストを右へ寄せる',
                'Justify cell text': 'セルのテキストを両端へ揃える',
                'Align cell text to the top': 'セルのテキストを上に寄せる',
                'Align cell text to the middle': 'セルのテキストを中央へ揃える',
                'Align cell text to the bottom': 'セルのテキストを下に寄せる',
                'Alignment': '配置',
                'Align table to the left': "テーブルを左へ寄せる",
                'Center table': "テーブルを中央へ寄せる",
                'Align table to the right': "テーブルを右へ寄せる",
                'Disc': '黒い丸',
                'Circle': '白い丸',
                'Square': '黒い四角',
                'Decimal': '10進数の数値',
                'Decimal with leading zero': '10進数の数値の前に0がつく',
                'Lower–roman': '小文字ローマ数字',
                'Upper-roman': '大文字ローマ数字',
                'Lower-latin': '小文字アルファベット',
                'Upper-latin': '大文字アルファベット'
            }),
                t.getPluralForm=function(e){return 0}}(window.CKEDITOR_TRANSLATIONS||(window.CKEDITOR_TRANSLATIONS={}))
            ;
        },
        getContent(value) {
            this.editorData = value
        },
        emitData() {
            this.$emit('updateContent', this.editorData)
        }
    },
}
</script>

<style>
#editor {
    width: 100%;
}
.ck-editor__editable_inline {
    min-height: 250px;
}

.ck.ck-balloon-panel {
    z-index: 99999999 !important;
}
#editor a {
    color: #1b3af2;
}
</style>
