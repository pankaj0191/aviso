<template>

    <label class="m-b-lg" style="width: 100%">
        <input type="radio"
            :value="val" 
            :name="name"
            :checked="val == value" 
            @change="$emit('input', val)" 
            class="card-input-element hidden">
        <div class="category-card" :class="classes">
            <div class="category-image">
                <slot name="image-svg"></slot>
            </div>
            <div class="category-title">{{title}}</div>
            <div class="category-body"><slot name="custom"></slot></div>
        </div>
    </label>
</template>

<script>
    export default {
        props: ['title', 'value', 'val', 'name', 'classes']
    }
</script>

<style lang="scss" scoped>
    .card-input-element:checked+.category-card::before {
        content: '\f058';
        color: #72A78F;
        font-family: 'FontAwesome';
        position: absolute;
        top: 12px;
        right: 12px;
        font-size: 16px;
        padding-right: 8px;
        padding-left: 4px;
        -webkit-animation-name: fadeInCheckbox;
        animation-name: fadeInCheckbox;
        -webkit-animation-duration: .5s;
        animation-duration: .5s;
        -webkit-animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
    

    .category-card:hover {
        background-color: #151f2b;
    }

    .card-input-element:checked+.category-card {
        border-inline: 5px solid #72A78F;
    }

    .category-card {

        &.light {
            background-color: #fff;
            color: #223345;
            border: 1px solid #223345;

            &:hover {
                background-color: #ecfef7;
            }
        }

        &.dark {
            background-color: #223345;
            color: #ecfef7;

             &:hover {
                background-color: #151f2b;
            }
        }
        min-height: 300px;
        height: 300px;
        cursor: pointer;
        user-select: none;
        border-radius: 16px;
        text-align: center;
        padding: 32px;
        position: relative;
        transition: 0.3s;
        display: flex;
        justify-content: space-between;
        flex-direction: column;

        .category-selected {
            position: absolute;
            top: 16px;
            right: 16px;
            border: 1px solid #72A78F;
            width: 24px;
            height: 24px;
            border-radius: 100%;
        }

        .category-image {
            padding: 24px;
        }

        .category-title {
            font-weight: 700;
        }
        .category-body {
            font-weight: 400;
            margin-top: 8px;
        }

        .custom-dimantions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;

            .custom-input {
                width: 70px;
                border: none;
                line-height: 2;
                background-color: #235066;
                border-radius: 4px;
                padding-left: 6px;
                padding-right: 6px;
                text-align: center;
                color: #649483;
                outline: none;
            }

        }
    }

    @-webkit-keyframes fadeInCheckbox {
        from {
            opacity: 0;
            -webkit-transform: rotateZ(-20deg);
        }

        to {
            opacity: 1;
            -webkit-transform: rotateZ(0deg);
        }
    }

    @keyframes fadeInCheckbox {
        from {
            opacity: 0;
            transform: rotateZ(-20deg);
        }

        to {
            opacity: 1;
            transform: rotateZ(0deg);
        }
    }
</style>